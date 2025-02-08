<?php

declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Tests\Unit\Service;

use ManniMedia\OpenaiChatbot\Service\RateLimitService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\CacheManager;

class RateLimitServiceTest extends UnitTestCase
{
    protected RateLimitService $subject;
    protected FrontendInterface $cacheMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheMock = $this->createMock(FrontendInterface::class);
        
        $cacheManagerMock = $this->createMock(CacheManager::class);
        $cacheManagerMock
            ->method('getCache')
            ->willReturn($this->cacheMock);

        $this->subject = $this->getAccessibleMock(
            RateLimitService::class,
            ['initializeObject'],
            [],
            '',
            false
        );
        $this->subject->_set('cache', $this->cacheMock);
    }

    /**
     * @test
     */
    public function isLimitExceededReturnsTrueWhenLimitIsReached(): void
    {
        $this->cacheMock
            ->method('get')
            ->willReturn(['count' => 51, 'timestamp' => time()]);

        self::assertTrue($this->subject->isLimitExceeded('test-user'));
    }

    /**
     * @test
     */
    public function isLimitExceededReturnsFalseWhenUnderLimit(): void
    {
        $this->cacheMock
            ->method('get')
            ->willReturn(['count' => 49, 'timestamp' => time()]);

        self::assertFalse($this->subject->isLimitExceeded('test-user'));
    }
}
