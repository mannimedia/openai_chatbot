<?php

declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LoggingService
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)
            ->getLogger(__CLASS__);
    }

    public function logError(string $message, array $data = []): void
    {
        $this->logger->error($message, $data);
    }

    public function logWarning(string $message, array $data = []): void
    {
        $this->logger->warning($message, $data);
    }

    public function logInfo(string $message, array $data = []): void
    {
        $this->logger->info($message, $data);
    }
}
