<?php

declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Tests\Unit\Service;

use ManniMedia\OpenaiChatbot\Service\QuotaService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class QuotaServiceTest extends UnitTestCase
{
    protected QuotaService $subject;
    protected FrontendInterface $cacheMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheMock = $this->createMock(FrontendInterface::class);
        $this->subject = new QuotaService($this->cacheMock);
    }

    /**
     * @test
     */
    public function checkQuotaReturnsFalseWhenLimitReached(): void
    {
        $this->cacheMock
            ->method('get')
            ->willReturn(['usage' => 1000, 'limit' => 1000]);

        self::assertFalse($this->subject->checkQuota('test_user'));
    }

    /**
     * @test
     */
    public function incrementQuotaUpdatesUsageCorrectly(): void
    {
        $this->cacheMock
            ->expects(self::once())
            ->method('set');

        $this->subject->incrementQuota('test_user', 1);
    }
}
