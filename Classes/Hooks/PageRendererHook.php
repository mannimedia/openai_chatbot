<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Hooks;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Core\Http\ApplicationType;

class PageRendererHook
{
    protected ?UriBuilder $uriBuilder = null;

    public function addChatBot(array &$params, PageRenderer $pageRenderer): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return;
        }

        $extbaseRequestParameters = GeneralUtility::makeInstance(ExtbaseRequestParameters::class);
        $extbaseRequestParameters->setControllerExtensionName('OpenaiChatbot');
        $extbaseRequestParameters->setControllerName('Chat');
        $extbaseRequestParameters->setPluginName('Chat');

        // Request mit ExtbaseAttribute erstellen
        $request = $request->withAttribute('extbase', $extbaseRequestParameters);
        $extbaseRequest = GeneralUtility::makeInstance(Request::class, $request);

        // StandaloneView erstellen
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setRequest($extbaseRequest);

        $view->setLayoutRootPaths(['EXT:openai_chatbot/Resources/Private/Layouts/']);
        $view->setTemplateRootPaths(['EXT:openai_chatbot/Resources/Private/Templates/']);
        $view->setPartialRootPaths(['EXT:openai_chatbot/Resources/Private/Partials/']);
        $view->setTemplate('Chat/Widget');

        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this->uriBuilder->setRequest($extbaseRequest);

        $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageType(1709343142)
            ->uriFor('conversationAjax', [], 'Chat', 'OpenaiChatbot');

        $view->assign('ajaxUrl', $uri);

        $chatbotHtml = $view->render();
        $pageRenderer->addCssFile('EXT:openai_chatbot/Resources/Public/Css/chat.css');
        $pageRenderer->addJsFile('EXT:openai_chatbot/Resources/Public/JavaScript/chat.js');
        $params['footerData'][] = $chatbotHtml;
    }
}