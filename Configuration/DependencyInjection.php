<?php
// Configuration/DependencyInjection.php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $container): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('ManniMedia\\OpenaiChatbot\\', '../Classes/*')
        ->exclude('../Classes/Domain/Model/*');

    $services->set(ManniMedia\OpenaiChatbot\Service\OpenAIService::class)
        ->public();
};