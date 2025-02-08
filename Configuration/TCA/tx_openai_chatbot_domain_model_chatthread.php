<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang_db.xlf:tx_openai_chatbot_domain_model_chatthread',
        'label' => 'session_id',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'session_id',
        'iconfile' => 'EXT:openai_chatbot/Resources/Public/Icons/tx_openai_chatbot_domain_model_chatthread.svg'
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, session_id, last_activity'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'session_id' => [
            'exclude' => false,
            'label' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang_db.xlf:tx_openai_chatbot_domain_model_chatthread.session_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'required' => true,
            ]
        ],
        'last_activity' => [
            'exclude' => false,
            'label' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang_db.xlf:tx_openai_chatbot_domain_model_chatthread.last_activity',
            'config' => [
                'type' => 'number',
                'size' => 11,
                'default' => 0
            ]
        ]
    ]
];