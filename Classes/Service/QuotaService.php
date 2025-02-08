<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class QuotaService
{
    private const TABLE_NAME = 'tx_openai_chatbot_quota';

    public function trackUsage(int $userId, int $tokens): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE_NAME);

        $currentMonth = date('Y-m');
        
        // Update or insert quota record
        $connection->executeStatement(
            'INSERT INTO ' . self::TABLE_NAME . ' (user_id, month, tokens_used) 
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE tokens_used = tokens_used + VALUES(tokens_used)',
            [$userId, $currentMonth, $tokens]
        );
    }

    public function getCurrentUsage(int $userId): array
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE_NAME);

        $currentMonth = date('Y-m');
        
        $usage = $connection->select(
            ['*'],
            self::TABLE_NAME,
            ['user_id' => $userId, 'month' => $currentMonth]
        )->fetch();

        return $usage ?: ['tokens_used' => 0];
    }

    public function checkQuota(int $userId, int $limit): bool
    {
        $usage = $this->getCurrentUsage($userId);
        return $usage['tokens_used'] < $limit;
    }

    public function resetMonthlyQuota(): void
    {
        // This could be called by a scheduler task at the beginning of each month
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE_NAME);

        $lastMonth = date('Y-m', strtotime('-1 month'));
        
        // Archive last month's data (optional)
        $connection->insert(
            'tx_openai_chatbot_quota_archive',
            ['month' => $lastMonth]
        );

        // Clear current quota
        $connection->truncate(self::TABLE_NAME);
    }
}
