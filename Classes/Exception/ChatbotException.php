<?php
namespace ManniMedia\OpenaiChatbot\Exception;

class ChatbotException extends \Exception
{
    public static function apiError(string $message): self
    {
        return new self($message, 1707308435);
    }
}
