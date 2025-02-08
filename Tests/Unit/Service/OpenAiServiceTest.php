<?php

namespace ManniMedia\OpenaiChatbot\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use ManniMedia\OpenaiChatbot\Service\OpenAiService;

class OpenAiServiceTest extends TestCase
{
    use ProphecyTrait;

    protected ObjectProphecy $requestFactory;
    protected ObjectProphecy $response;
    protected ObjectProphecy $stream;
    protected OpenAiService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock ExtensionConfiguration
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openai_chatbot'] = [
            'apiKey' => 'test-api-key',
            'assistantId' => 'test-assistant-id',
            'model' => 'gpt-4'
        ];

        // Mock RequestFactory
        $this->requestFactory = $this->prophesize(RequestFactory::class);
        $this->response = $this->prophesize(ResponseInterface::class);
        $this->stream = $this->prophesize(StreamInterface::class);

        // Create subject
        $this->subject = new OpenAiService($this->requestFactory->reveal());
    }

    /**
     * @test
     */
    public function createThreadReturnsThreadId(): void
    {
        $threadResponse = ['id' => 'thread_abc123'];

        $this->stream->getContents()->willReturn(json_encode($threadResponse));
        $this->response->getStatusCode()->willReturn(200);
        $this->response->getBody()->willReturn($this->stream->reveal());

        $this->requestFactory
            ->request(
                'POST',
                'https://api.openai.com/v1/threads',
                $this->getExpectedRequestOptions()
            )
            ->willReturn($this->response->reveal());

        $threadId = $this->subject->createThread();
        $this->assertEquals('thread_abc123', $threadId);
    }

    /**
     * @test
     */
    public function sendMessageReturnsFormattedResponse(): void
    {
        // Mock thread creation
        $threadResponse = ['id' => 'thread_abc123'];
        $threadStream = $this->prophesize(StreamInterface::class);
        $threadStream->getContents()->willReturn(json_encode($threadResponse));
        $threadResponse = $this->prophesize(ResponseInterface::class);
        $threadResponse->getStatusCode()->willReturn(200);
        $threadResponse->getBody()->willReturn($threadStream->reveal());

        $this->requestFactory
            ->request(
                'POST',
                'https://api.openai.com/v1/threads',
                $this->getExpectedRequestOptions()
            )
            ->willReturn($threadResponse->reveal());

        // Mock message creation
        $messageResponse = ['id' => 'msg_123'];
        $messageStream = $this->prophesize(StreamInterface::class);
        $messageStream->getContents()->willReturn(json_encode($messageResponse));
        $messageResp = $this->prophesize(ResponseInterface::class);
        $messageResp->getStatusCode()->willReturn(200);
        $messageResp->getBody()->willReturn($messageStream->reveal());
        
        $this->requestFactory
            ->request(
                'POST',
                'https://api.openai.com/v1/threads/thread_abc123/messages',
                $this->getExpectedRequestOptions(['role' => 'user', 'content' => 'test message'])
            )
            ->willReturn($messageResp->reveal());

        // Mock run creation
        $runResponse = ['id' => 'run_123', 'status' => 'queued'];
        $runStream = $this->prophesize(StreamInterface::class);
        $runStream->getContents()->willReturn(json_encode($runResponse));
        $runResp = $this->prophesize(ResponseInterface::class);
        $runResp->getStatusCode()->willReturn(200);
        $runResp->getBody()->willReturn($runStream->reveal());
        
        $this->requestFactory
            ->request(
                'POST',
                'https://api.openai.com/v1/threads/thread_abc123/runs',
                $this->getExpectedRequestOptions([
                    'assistant_id' => 'test-assistant-id',
                    'instructions' => "You are a helpful TYPO3 support assistant.\nProvide clear and concise answers.\nUse markdown formatting when appropriate.\nIf you need to show code, use proper syntax highlighting.\nRespond in en language."
                ])
            )
            ->willReturn($runResp->reveal());

        // Mock run status check
        $runStatusResponse = ['status' => 'completed'];
        $statusStream = $this->prophesize(StreamInterface::class);
        $statusStream->getContents()->willReturn(json_encode($runStatusResponse));
        $statusResp = $this->prophesize(ResponseInterface::class);
        $statusResp->getStatusCode()->willReturn(200);
        $statusResp->getBody()->willReturn($statusStream->reveal());
        
        $this->requestFactory
            ->request(
                'GET',
                'https://api.openai.com/v1/threads/thread_abc123/runs/run_123',
                $this->getExpectedRequestOptions()
            )
            ->willReturn($statusResp->reveal());

        // Mock messages retrieval
        $messagesResponse = [
            'data' => [
                [
                    'role' => 'assistant',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => [
                                'value' => 'Test response'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $messagesStream = $this->prophesize(StreamInterface::class);
        $messagesStream->getContents()->willReturn(json_encode($messagesResponse));
        $messagesResp = $this->prophesize(ResponseInterface::class);
        $messagesResp->getStatusCode()->willReturn(200);
        $messagesResp->getBody()->willReturn($messagesStream->reveal());
        
        $this->requestFactory
            ->request(
                'GET',
                'https://api.openai.com/v1/threads/thread_abc123/messages',
                $this->getExpectedRequestOptions()
            )
            ->willReturn($messagesResp->reveal());

        $response = $this->subject->sendMessage('test message');
        
        $this->assertArrayHasKey('choices', $response);
        $this->assertEquals('Test response', $response['choices'][0]['message']['content']);
    }

    /**
     * @test
     */
    public function sendMessageThrowsExceptionOnApiError(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API request failed: Unauthorized');

        $this->response->getStatusCode()->willReturn(401);
        $this->response->getReasonPhrase()->willReturn('Unauthorized');

        $this->requestFactory
            ->request(
                'POST',
                'https://api.openai.com/v1/threads',
                $this->getExpectedRequestOptions()
            )
            ->willReturn($this->response->reveal());

        $this->subject->sendMessage('test message');
    }

    protected function getExpectedRequestOptions(array $data = []): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer test-api-key',
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v1'
            ]
        ];

        if (!empty($data)) {
            $options['body'] = json_encode($data);
        }

        return $options;
    }
}
