<?php

declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Tests\Unit\Service;

use ManniMedia\OpenaiChatbot\Service\ChatSessionService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class ChatSessionServiceTest extends UnitTestCase
{
    protected ChatSessionService $subject;
    protected FrontendUserAuthentication $userAuthMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userAuthMock = $this->createMock(FrontendUserAuthentication::class);
        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->fe_user = $this->userAuthMock;

        $this->subject = new ChatSessionService();
    }

    /**
     * @test
     */
    public function saveMessageStoresMessageInSession(): void
    {
        $message = ['role' => 'user', 'content' => 'test message'];
        
        $this->userAuthMock
            ->expects(self::once())
            ->method('setKey');
            
        $this->userAuthMock
            ->expects(self::once())
            ->method('storeSessionData');

        $this->subject->saveMessage($message);
    }
}
