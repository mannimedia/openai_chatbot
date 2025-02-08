<?php
namespace ManniMedia\OpenaiChatbot\EventListener;

use ManniMedia\OpenaiChatbot\Event\BeforeChatMessageEvent;

final class ChatMessageListener
{
    #[AsEventListener]
    public function __invoke(BeforeChatMessageEvent $event): void
    {
        // Event handling implementation
    }
}
