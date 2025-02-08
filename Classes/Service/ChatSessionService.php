<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ChatSessionService implements SingletonInterface
{
    private const SESSION_KEY = 'tx_openai_chatbot_messages';

    private function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }

    private function getFrontendUser(): ?object
    {
        return $this->getTypoScriptFrontendController()?->fe_user;
    }

    public function addMessage(string $role, string $content): void
    {
        if ($feUser = $this->getFrontendUser()) {
            $messages = $this->getMessages();
            $messages[] = [
                'role' => $role,
                'content' => $content,
                'timestamp' => time()
            ];
            $feUser->setKey('ses', self::SESSION_KEY, $messages);
            $feUser->storeSessionData();
        }
    }

    public function getMessages(): array
    {
        if ($feUser = $this->getFrontendUser()) {
            return $feUser->getKey('ses', self::SESSION_KEY) ?? [];
        }
        return [];
    }

    public function clearMessages(): void
    {
        if ($feUser = $this->getFrontendUser()) {
            $feUser->setKey('ses', self::SESSION_KEY, []);
            $feUser->storeSessionData();
        }
    }
}