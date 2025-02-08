<?php
return [
    'openai_chatbot_message' => [
        'path' => '/openai-chatbot/message',
        'target' => \ManniMedia\OpenaiChatbot\Controller\ChatController::class . '::messageAction'
    ],
    'openai_chatbot_initiate' => [
        'path' => '/openai-chatbot/initiate',
        'target' => \ManniMedia\OpenaiChatbot\Controller\ChatController::class . '::initiateAction'
    ]
];
