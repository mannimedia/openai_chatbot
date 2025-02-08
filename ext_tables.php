<?php
defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'openai_chatbot',
    'Configuration/TypoScript',
    'OpenAI Chatbot'
);

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'OpenaiChatbot',
        'web',
        'chatbot',
        '',
        [
            \ManniMedia\OpenaiChatbot\Controller\AdminController::class => 'list, show, delete, archive',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:openai_chatbot/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang_mod.xlf',
        ]
    );
})();
