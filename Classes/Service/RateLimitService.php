<?php

declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RateLimitService
{
    private FrontendInterface $cache;
    private const MAX_REQUESTS = 50; // Maximale Anfragen pro Zeitfenster
    private const TIME_WINDOW = 3600; // Zeitfenster in Sekunden (1 Stunde)

    public function __construct()
    {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('openai_chatbot');
    }

    public function isLimitExceeded(string $identifier): bool
    {
        $cacheId = 'ratelimit_' . $identifier;
        $current = $this->cache->get($cacheId) ?? ['count' => 0, 'timestamp' => time()];

        // Prüfen ob das Zeitfenster abgelaufen ist
        if (time() - $current['timestamp'] > self::TIME_WINDOW) {
            $current = ['count' => 0, 'timestamp' => time()];
        }

        $current['count']++;
        $this->cache->set($cacheId, $current, [], self::TIME_WINDOW);

        return $current['count'] > self::MAX_REQUESTS;
    }

    public function getRemainingRequests(string $identifier): int
    {
        $cacheId = 'ratelimit_' . $identifier;
        $current = $this->cache->get($cacheId) ?? ['count' => 0];
        return max(0, self::MAX_REQUESTS - $current['count']);
    }
}
