<?php

namespace ManniMedia\OpenaiChatbot\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class ChatThread extends AbstractEntity
{
    protected string $threadId = '';
    protected string $assistantId = '';
    protected string $messages = '';
    protected int $lastActivity = 0;

    public function getThreadId(): string
    {
        return $this->threadId;
    }

    public function setThreadId(string $threadId): void
    {
        $this->threadId = $threadId;
    }

    public function getAssistantId(): string
    {
        return $this->assistantId;
    }

    public function setAssistantId(string $assistantId): void
    {
        $this->assistantId = $assistantId;
    }

    public function getMessages(): string
    {
        return $this->messages;
    }

    public function setMessages(string $messages): void
    {
        $this->messages = $messages;
    }

    public function getLastActivity(): int
    {
        return $this->lastActivity;
    }

    public function setLastActivity(int $lastActivity): void
    {
        $this->lastActivity = $lastActivity;
    }
}
