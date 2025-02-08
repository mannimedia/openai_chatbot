<?php
defined('TYPO3') or die();

// Füge das neue Feld zur pages Tabelle hinzu
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', [
    'tx_openai_chatbot_disabled' => [
        'exclude' => true,
        'label' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang_db.xlf:pages.tx_openai_chatbot_disabled',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    'label' => '',
                    'value' => ''
                ]
            ],
        ],
    ],
]);

// Füge das Feld zur Palette hinzu
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'miscellaneous',
    'tx_openai_chatbot_disabled',
    'after:no_search'
);