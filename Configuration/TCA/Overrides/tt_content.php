<?php
defined('TYPO3') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'OpenaiChatbot',
    'Chat',
    'OpenAI Chatbot',
    'EXT:openai_chatbot/Resources/Public/Icons/Extension.svg'
);