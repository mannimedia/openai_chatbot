<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use TYPO3\CMS\Core\SingletonInterface;

class ContextManagementService implements SingletonInterface
{
    private const MAX_CONTEXT_LENGTH = 4096; // Tokens
    private const SUMMARY_PROMPT = "Summarize the key points of this conversation while preserving important details: ";

    public function processContext(array $messages, string $newMessage): array
    {
        $context = $messages;
        $context[] = ['role' => 'user', 'content' => $newMessage];

        // If context is too long, summarize older messages
        if ($this->estimateTokens($context) > self::MAX_CONTEXT_LENGTH) {
            return $this->summarizeContext($context);
        }

        return $context;
    }

    private function summarizeContext(array $messages): array
    {
        // Get older messages for summarization
        $oldMessages = array_slice($messages, 0, -5); // Keep last 5 messages as is
        $recentMessages = array_slice($messages, -5);

        // Create summary request
        $summaryContent = '';
        foreach ($oldMessages as $message) {
            $summaryContent .= $message['role'] . ': ' . $message['content'] . "\n";
        }

        // Get summary from OpenAI
        $summary = $this->getOpenAiSummary(self::SUMMARY_PROMPT . $summaryContent);

        // Return summarized context
        return array_merge(
            [['role' => 'system', 'content' => 'Previous conversation summary: ' . $summary]],
            $recentMessages
        );
    }

    private function estimateTokens(array $messages): int
    {
        $totalChars = 0;
        foreach ($messages as $message) {
            $totalChars += strlen($message['content']);
        }
        // Rough estimation: 4 characters per token
        return (int)($totalChars / 4);
    }

    private function getOpenAiSummary(string $content): string
    {
        // Implementation using OpenAiService
        // This would be injected via constructor
        return 'Summary placeholder';
    }

    public function extractEntities(string $message): array
    {
        // Extract important entities (names, dates, numbers, etc.)
        // Could be used for better context management
        return [];
    }

    public function detectIntent(string $message): string
    {
        // Detect user intent for better response handling
        return '';
    }

    public function getRelevantKnowledge(string $message): array
    {
        // Get relevant knowledge from TYPO3 pages/records
        return [];
    }
}
