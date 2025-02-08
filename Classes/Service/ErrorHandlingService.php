<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ErrorHandlingService
{
    private LoggerInterface $logger;
    private array $errorMessages;
    private bool $debugMode;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $this->loadErrorMessages();
        $this->debugMode = (bool)($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openai_chatbot']['debug'] ?? false);
    }

    private function loadErrorMessages(): void
    {
        $this->errorMessages = [
            'API_ERROR' => [
                'user' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang.xlf:error.api',
                'log' => 'OpenAI API error: %s',
                'severity' => 2
            ],
            'RATE_LIMIT' => [
                'user' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang.xlf:error.rate_limit',
                'log' => 'Rate limit exceeded for thread %s',
                'severity' => 1
            ],
            'QUOTA_EXCEEDED' => [
                'user' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang.xlf:error.quota_exceeded',
                'log' => 'Quota exceeded for user %s',
                'severity' => 2
            ],
            'INVALID_INPUT' => [
                'user' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang.xlf:error.invalid_input',
                'log' => 'Invalid input received: %s',
                'severity' => 1
            ],
            'CONTEXT_ERROR' => [
                'user' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang.xlf:error.context',
                'log' => 'Context processing error: %s',
                'severity' => 2
            ]
        ];
    }

    public function handleError(string $errorCode, array $parameters = [], ?\Throwable $exception = null): array
    {
        $errorConfig = $this->errorMessages[$errorCode] ?? null;
        if (!$errorConfig) {
            return $this->handleUnknownError($errorCode, $exception);
        }

        // Log error
        $logMessage = sprintf($errorConfig['log'], ...$parameters);
        if ($exception) {
            $logMessage .= ' Exception: ' . $exception->getMessage();
        }
        $this->logger->log($errorConfig['severity'], $logMessage);

        // Prepare user response
        $response = [
            'error' => true,
            'code' => $errorCode,
            'message' => $errorConfig['user']
        ];

        // Add debug information if enabled
        if ($this->debugMode && $exception) {
            $response['debug'] = [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        // Track error for analytics
        $this->trackError($errorCode, $parameters, $exception);

        return $response;
    }

    private function handleUnknownError(string $errorCode, ?\Throwable $exception): array
    {
        $logMessage = 'Unknown error occurred: ' . $errorCode;
        if ($exception) {
            $logMessage .= ' Exception: ' . $exception->getMessage();
        }
        $this->logger->error($logMessage);

        return [
            'error' => true,
            'code' => 'UNKNOWN_ERROR',
            'message' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang.xlf:error.unknown'
        ];
    }

    private function trackError(string $errorCode, array $parameters, ?\Throwable $exception): void
    {
        // Track error in analytics for monitoring
        try {
            GeneralUtility::makeInstance(AnalyticsService::class)->trackInteraction([
                'messageType' => 'error',
                'errorCode' => $errorCode,
                'parameters' => $parameters,
                'exceptionMessage' => $exception ? $exception->getMessage() : null
            ]);
        } catch (\Exception $e) {
            // Silent fail for analytics
            $this->logger->warning('Failed to track error in analytics: ' . $e->getMessage());
        }
    }

    public function isRecoverableError(string $errorCode): bool
    {
        return in_array($errorCode, ['RATE_LIMIT', 'QUOTA_EXCEEDED', 'INVALID_INPUT']);
    }

    public function getSuggestedAction(string $errorCode): ?string
    {
        $suggestions = [
            'RATE_LIMIT' => 'Wait a few minutes before trying again',
            'QUOTA_EXCEEDED' => 'Contact administrator to increase quota',
            'INVALID_INPUT' => 'Review and modify your input'
        ];

        return $suggestions[$errorCode] ?? null;
    }
}
