<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Http\Response;

class ChatBotAvailabilityMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Prüfe, ob es sich um eine Chat-API-Anfrage handelt
        if (str_contains($request->getUri()->getPath(), 'tx_openai_chatbot')) {
            $queryParams = $request->getQueryParams();
            $isDisabled = (bool)($queryParams['tx_openai_chatbot_disabled'] ?? false);

            if ($isDisabled) {
                $stream = new Stream('php://temp', 'wb+');
                $stream->write(json_encode([
                    'error' => 'Chat is currently disabled'
                ], JSON_THROW_ON_ERROR));

                return new Response(
                    $stream,
                    403,
                    ['Content-Type' => 'application/json']
                );
            }
        }

        return $handler->handle($request);
    }
}