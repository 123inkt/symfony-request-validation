<?php

declare(strict_types=1);

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\EventSubscriber\RequestValidationSubscriber;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintCollectionBuilder;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleParser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $services = $containerConfigurator->services();

    $services->set(ConstraintCollectionBuilder::class);
    $services->set(ConstraintResolver::class);
    $services->set(RuleParser::class);
    $services->set(ConstraintFactory::class);
    $services->set(RequestConstraintFactory::class);
    $services->set(RequestValidationSubscriber::class)->tag('kernel.event_subscriber');
};
