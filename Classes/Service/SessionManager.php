<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Service;

use TYPO3\CMS\Core\SingletonInterface;

class SessionManager implements SingletonInterface
{
    private const SESSION_KEY = 'tx_openai_chatbot_messages';

    public function getMessages(): array
    {
        return $this->getSessionData(self::SESSION_KEY) ?? [];
    }

    public function addMessage(string $role, string $content): void
    {
        $messages = $this->getMessages();
        $messages[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => time()
        ];
        $this->setSessionData(self::SESSION_KEY, $messages);
    }

    public function clearMessages(): void
    {
        $this->setSessionData(self::SESSION_KEY, []);
    }

    protected function getSessionData(string $key)
    {
        if (isset($GLOBALS['TSFE']->fe_user)) {
            return $GLOBALS['TSFE']->fe_user->getKey('ses', $key);
        }
        return null;
    }

    protected function setSessionData(string $key, $data): void
    {
        if (isset($GLOBALS['TSFE']->fe_user)) {
            $GLOBALS['TSFE']->fe_user->setKey('ses', $key, $data);
            $GLOBALS['TSFE']->fe_user->storeSessionData();
        }
    }
}