<?php
declare(strict_types=1);

return [
    'frontend' => [
        'mannimedia/openai-chatbot/availability' => [
            'target' => \ManniMedia\OpenaiChatbot\Middleware\ChatBotAvailabilityMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site'
            ],
        ],
        'mannimedia/openai-chatbot/session' => [
            'target' => \TYPO3\CMS\Frontend\Middleware\FrontendUserAuthenticator::class,
            'before' => [
                'typo3/cms-frontend/authentication'
            ],
        ],

    ]
];