<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Controller;

use ManniMedia\OpenaiChatbot\Service\OpenAIService;
use ManniMedia\OpenaiChatbot\Service\SessionManager;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ChatController extends ActionController
{
    protected SessionManager $sessionManager;
    protected OpenAIService $openAIService;

    public function __construct(
        SessionManager $sessionManager,
        OpenAIService $openAIService
    ) {
        $this->sessionManager = $sessionManager;
        $this->openAIService = $openAIService;
    }

    protected function getApiKey(): string
    {
        try {
            $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                ExtensionConfiguration::class
            );
            $apiKey = $extensionConfiguration->get('openai_chatbot', 'apiKey');

            if (empty($apiKey)) {
                throw new \RuntimeException('OpenAI API Key nicht konfiguriert');
            }

            return $apiKey;
        } catch (\Exception $e) {
            throw new \RuntimeException('Fehler beim Laden des API Keys: ' . $e->getMessage(), 1707396245);
        }
    }

    public function widgetAction(): ResponseInterface
    {
        try {
            $messages = $this->sessionManager->getMessages();
            $this->view->assign('messages', $messages);
            return $this->htmlResponse();
        } catch (\Exception $e) {
            return $this->htmlResponse('Ein Fehler ist aufgetreten: ' . $e->getMessage());
        }
    }

    public function conversationAjaxAction(): ResponseInterface
    {
        try {
            // Versuche zuerst POST-Parameter zu lesen
            $message = $this->request->getArgument('message');

            if (empty($message)) {
                // Falls keine POST-Parameter, versuche JSON zu lesen
                $jsonContent = file_get_contents('php://input');
                $data = json_decode($jsonContent, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($data['message'])) {
                    $message = $data['message'];
                }
            }

            if (empty($message)) {
                throw new \InvalidArgumentException('Keine Nachricht gefunden');
            }

            $message = trim($message);
            $response = $this->openAIService->sendMessage($message);

            return new JsonResponse([
                'success' => true,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function resetSessionAction(): ResponseInterface
    {
        try {
            $this->sessionManager->clearSession();
            return new JsonResponse([
                'success' => true,
                'message' => 'Session wurde zurückgesetzt'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Fehler beim Zurücksetzen der Session'
            ], 500);
        }
    }
}