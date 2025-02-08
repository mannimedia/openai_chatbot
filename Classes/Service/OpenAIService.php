<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use GuzzleHttp\Client;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OpenAIService
{
    protected string $apiKey;
    protected Client $client;
    protected ?string $assistantId = null;
    protected ?string $threadId = null;
    protected array $config;

    // Basis-Einstellungen
    protected string $chatBotName;
    protected int $maxTokens;
    protected float $temperature;
    protected string $model;
    protected string $assistantInstructions;

    // DSGVO & Sprache
    protected bool $enablePrivacyNotice;
    protected string $privacyNoticeText;
    protected string $defaultLanguage;

    // Style-Einstellungen
    protected string $chatButtonPosition;
    protected string $primaryColor;
    protected bool $enableDarkMode;

    public function __construct()
    {
        $this->initializeConfiguration();
        $this->initializeClient();
    }

    protected function initializeConfiguration(): void
    {
        try {
            $this->config = GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('openai_chatbot');

            // Basis-Einstellungen
            $this->apiKey = (string)($this->config['apiKey'] ?? '');
            $this->assistantId = $this->config['assistantId'] ?? null;
            $this->chatBotName = (string)($this->config['chatBotName'] ?? 'TYPO3 Assistant');
            $this->model = (string)($this->config['model'] ?? 'gpt-4-turbo-preview');
            $this->maxTokens = (int)($this->config['maxTokens'] ?? 2000);
            $this->temperature = (float)($this->config['temperature'] ?? 0.7);
            $this->assistantInstructions = (string)($this->config['assistantInstructions'] ??
                'Du bist ein hilfsbereicher TYPO3 Assistent. Antworte präzise und professionell.');

            // DSGVO & Sprache
            $this->enablePrivacyNotice = (bool)($this->config['enablePrivacyNotice'] ?? true);
            $this->privacyNoticeText = (string)($this->config['privacyNoticeText'] ?? '');
            $this->defaultLanguage = (string)($this->config['defaultLanguage'] ?? 'de');

            // Style-Einstellungen
            $this->chatButtonPosition = (string)($this->config['chatButtonPosition'] ?? 'bottom-right');
            $this->primaryColor = (string)($this->config['primaryColor'] ?? '#0074D9');
            $this->enableDarkMode = (bool)($this->config['enableDarkMode'] ?? false);

        } catch (ExtensionConfigurationExtensionNotConfiguredException $e) {
            throw new \RuntimeException('OpenAI Konfiguration nicht gefunden', 1644123456);
        }

        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenAI API Key ist nicht konfiguriert', 1644123457);
        }
    }

    protected function initializeClient(): void
    {
        $this->client = GeneralUtility::makeInstance(Client::class, [
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2'
            ],
        ]);
    }

    public function sendMessage(string $message, ?string $threadId = null): array
    {
        if ($threadId === null) {
            $threadId = $this->createThread();
        }

        // Nachricht zum Thread hinzufügen
        $messageResponse = $this->client->post("threads/{$threadId}/messages", [
            'json' => [
                'role' => 'user',
                'content' => $message
            ]
        ]);

        // Run erstellen und ausführen
        $runResponse = $this->client->post("threads/{$threadId}/runs", [
            'json' => [
                'assistant_id' => $this->getAssistantId()
            ]
        ]);

        $runResult = json_decode((string)$runResponse->getBody(), true);
        $status = $this->waitForRunCompletion($threadId, $runResult['id']);

        if ($status !== 'completed') {
            throw new \RuntimeException("Chat-Anfrage fehlgeschlagen: {$status}");
        }

        // Antwort abrufen
        $messagesResponse = $this->client->get("threads/{$threadId}/messages", [
            'query' => ['limit' => 1, 'order' => 'desc']
        ]);

        $messages = json_decode((string)$messagesResponse->getBody(), true);
        $response = $messages['data'][0]['content'][0]['text']['value'] ?? '';

        return [
            'threadId' => $threadId,
            'message' => $response
        ];
    }

    protected function waitForRunCompletion(string $threadId, string $runId): string
    {
        $maxAttempts = 30;
        $attempt = 0;

        do {
            if ($attempt++ >= $maxAttempts) {
                throw new \RuntimeException('Zeitüberschreitung bei der Chat-Anfrage');
            }

            sleep(1);
            $response = $this->client->get("threads/{$threadId}/runs/{$runId}");
            $result = json_decode((string)$response->getBody(), true);
            $status = $result['status'];

        } while (!in_array($status, ['completed', 'failed', 'cancelled', 'expired']));

        return $status;
    }

    protected function getAssistantId(): string
    {
        if ($this->assistantId === null) {
            $this->assistantId = $this->createAssistant();
        }
        return $this->assistantId;
    }

    protected function createAssistant(): string
    {
        $instructions = sprintf(
            "%s\nSprache: %s\nName: %s",
            $this->assistantInstructions,
            $this->defaultLanguage,
            $this->chatBotName
        );

        $response = $this->client->post('assistants', [
            'json' => [
                'model' => $this->model,
                'name' => $this->chatBotName,
                'instructions' => $instructions,
                'tools' => [],
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens
            ]
        ]);

        $result = json_decode((string)$response->getBody(), true);
        return $result['id'];
    }

    protected function createThread(): string
    {
        $response = $this->client->post('threads');
        $result = json_decode((string)$response->getBody(), true);
        return $result['id'];
    }

    // Getter-Methoden
    public function getChatBotName(): string
    {
        return $this->chatBotName;
    }

    public function getChatButtonPosition(): string
    {
        return $this->chatButtonPosition;
    }

    public function getPrimaryColor(): string
    {
        return $this->primaryColor;
    }

    public function isEnableDarkMode(): bool
    {
        return $this->enableDarkMode;
    }

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    public function isPrivacyNoticeEnabled(): bool
    {
        return $this->enablePrivacyNotice;
    }

    public function getPrivacyNoticeText(): string
    {
        return $this->privacyNoticeText;
    }
}