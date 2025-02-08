<?php

declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Tests\Unit\Service;

use ManniMedia\OpenaiChatbot\Service\AnalyticsService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

class AnalyticsServiceTest extends UnitTestCase
{
    protected AnalyticsService $subject;
    protected ConnectionPool $connectionPoolMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connectionPoolMock = $this->createMock(ConnectionPool::class);
        $this->subject = new AnalyticsService($this->connectionPoolMock);
    }

    /**
     * @test
     */
    public function logInteractionAddsDataCorrectly(): void
    {
        $connectionMock = $this->createMock(Connection::class);
        $this->connectionPoolMock
            ->method('getConnectionForTable')
            ->willReturn($connectionMock);

        $connectionMock
            ->expects(self::once())
            ->method('insert');

        $this->subject->logInteraction('test_action', ['data' => 'test']);
    }
}
