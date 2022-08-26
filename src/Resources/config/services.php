<?php

declare(strict_types=1);

use DigitalRevolution\SymfonyRequestValidation\EventSubscriber\RequestValidationSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()->set(RequestValidationSubscriber::class)->tag('kernel.event_subscriber');
};
