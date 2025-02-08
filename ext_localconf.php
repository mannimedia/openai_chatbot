<?php

defined('TYPO3') or die();

(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        'chatbotAjaxPage = PAGE
    chatbotAjaxPage {
        typeNum = 1709343142
        config {
            disableAllHeaderCode = 1
            additionalHeaders.10.header = Content-Type: application/json
            admPanel = 0
            debug = 0
            no_cache = 1
        }
    }'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
        plugin.tx_openai_chatbot {
            settings {
                sessionHandling = 1
            }
            persistence {
                storagePid = {$plugin.tx_openai_chatbot.persistence.storagePid}
            }
        }
    ');
    // Session für Frontend aktivieren
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'openai_chatbot',
        'setup',
        "
        plugin.tx_openai_chatbot {
            features {
                requireCHashArgumentForActionArguments = 0
            }
            settings {
                sessionHandling = 1
            }
        }
        "
    );
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['openai_chatbot'] = [
        'apiKey' => $_ENV['OPENAI_API_KEY'] ?? '',
    ];
    // Session-Handling Middleware registrieren
    $GLOBALS['TYPO3_CONF_VARS']['FE']['middlewares']['openai-chatbot-session'] = [
        'target' => \TYPO3\CMS\Frontend\Middleware\FrontendUserAuthenticator::class,
        'before' => ['typo3/cms-frontend/authentication'],
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] =
        \ManniMedia\OpenaiChatbot\Hooks\PageRendererHook::class . '->addChatBot';


    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'OpenaiChatbot',
        'Chat',
        [
            \ManniMedia\OpenaiChatbot\Controller\ChatController::class => 'widget,conversationAjax'
        ],
        [
            \ManniMedia\OpenaiChatbot\Controller\ChatController::class => 'conversationAjax'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'OpenaiChatbot',
        'Admin',
        [
            \ManniMedia\OpenaiChatbot\Controller\AdminController::class => 'list,show',
        ],
        [
            \ManniMedia\OpenaiChatbot\Controller\AdminController::class => '',
        ]
    );

    // Register Icons
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'openai-chatbot-plugin',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:openai_chatbot/Resources/Public/Icons/Plugin.svg']
    );

    // Add page TSconfig
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '@import "EXT:openai_chatbot/Configuration/TsConfig/Page/Mod/Wizards/NewContentElement.tsconfig"'
    );
})();

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['openai_chatbot'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['openai_chatbot'] = [];
}
