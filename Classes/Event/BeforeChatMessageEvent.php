<?php
namespace ManniMedia\OpenaiChatbot\Event;

final class BeforeChatMessageEvent
{
    public function __construct(
        private readonly string $message,
        private readonly int $userId
    ) {}

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
