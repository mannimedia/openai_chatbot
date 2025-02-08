<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AnalyticsService
{
    public function trackInteraction(array $data): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_openai_chatbot_analytics');

        $connection->insert('tx_openai_chatbot_analytics', [
            'thread_id' => $data['threadId'],
            'user_id' => $data['userId'] ?? 0,
            'message_type' => $data['messageType'],
            'tokens_used' => $data['tokensUsed'] ?? 0,
            'response_time' => $data['responseTime'] ?? 0,
            'sentiment' => $data['sentiment'] ?? 'neutral',
            'intent' => $data['intent'] ?? '',
            'timestamp' => time(),
            'page_uid' => $data['pageUid'] ?? 0
        ]);
    }

    public function generateReport(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_openai_chatbot_analytics');

        $constraints = [];
        if ($startDate) {
            $constraints[] = $qb->expr()->gte('timestamp', $startDate->getTimestamp());
        }
        if ($endDate) {
            $constraints[] = $qb->expr()->lte('timestamp', $endDate->getTimestamp());
        }

        return [
            'usage' => $this->getUsageStats($qb, $constraints),
            'performance' => $this->getPerformanceMetrics($qb, $constraints),
            'userEngagement' => $this->getUserEngagementMetrics($qb, $constraints),
            'contentAnalysis' => $this->getContentAnalysis($qb, $constraints)
        ];
    }

    private function getUsageStats($qb, array $constraints): array
    {
        return [
            'totalChats' => $this->getTotalChats($qb, $constraints),
            'totalTokens' => $this->getTotalTokens($qb, $constraints),
            'averageTokensPerChat' => $this->getAverageTokensPerChat($qb, $constraints),
            'peakUsageTimes' => $this->getPeakUsageTimes($qb, $constraints)
        ];
    }

    private function getPerformanceMetrics($qb, array $constraints): array
    {
        return [
            'averageResponseTime' => $this->getAverageResponseTime($qb, $constraints),
            'errorRate' => $this->getErrorRate($qb, $constraints),
            'successRate' => $this->getSuccessRate($qb, $constraints)
        ];
    }

    private function getUserEngagementMetrics($qb, array $constraints): array
    {
        return [
            'activeUsers' => $this->getActiveUsers($qb, $constraints),
            'averageSessionLength' => $this->getAverageSessionLength($qb, $constraints),
            'returnRate' => $this->getReturnRate($qb, $constraints)
        ];
    }

    private function getContentAnalysis($qb, array $constraints): array
    {
        return [
            'popularTopics' => $this->getPopularTopics($qb, $constraints),
            'sentimentAnalysis' => $this->getSentimentAnalysis($qb, $constraints),
            'commonIntents' => $this->getCommonIntents($qb, $constraints)
        ];
    }

    // Implementation of individual metric methods...
    private function getTotalChats($qb, array $constraints): int
    {
        $qb->count('thread_id')
           ->from('tx_openai_chatbot_analytics')
           ->where(...$constraints);
        
        return (int)$qb->executeQuery()->fetchOne();
    }

    private function getAverageResponseTime($qb, array $constraints): float
    {
        $qb->selectLiteral('AVG(response_time) as avg_time')
           ->from('tx_openai_chatbot_analytics')
           ->where(...$constraints);
        
        return (float)$qb->executeQuery()->fetchOne();
    }

    // ... weitere Implementierungen der Metrik-Methoden
}
