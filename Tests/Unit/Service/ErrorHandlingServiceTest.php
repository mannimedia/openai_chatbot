<?php

declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Tests\Unit\Service;

use ManniMedia\OpenaiChatbot\Service\ErrorHandlingService;
use ManniMedia\OpenaiChatbot\Service\LoggingService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ErrorHandlingServiceTest extends UnitTestCase
{
    protected ErrorHandlingService $subject;
    protected LoggingService $loggingServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->loggingServiceMock = $this->createMock(LoggingService::class);
        $this->subject = new ErrorHandlingService($this->loggingServiceMock);
    }

    /**
     * @test
     */
    public function handleExceptionLogsErrorAndReturnsErrorMessage(): void
    {
        $exception = new \Exception('Test error');
        
        $this->loggingServiceMock
            ->expects(self::once())
            ->method('logError');

        $result = $this->subject->handleException($exception);
        
        self::assertIsArray($result);
        self::assertArrayHasKey('error', $result);
        self::assertFalse($result['success']);
    }
}
