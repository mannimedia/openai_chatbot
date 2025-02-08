<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'OpenAI Chatbot',
    'description' => 'TYPO3 Extension für OpenAI Chatbot Integration',
    'category' => 'plugin',
    'author' => 'Manni Rössler',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'ManniMedia\\OpenaiChatbot\\' => 'Classes'
        ]
    ]
];