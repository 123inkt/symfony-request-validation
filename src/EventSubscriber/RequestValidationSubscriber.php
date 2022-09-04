<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\EventSubscriber;

use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestValidationSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER_ARGUMENTS => ['handleArguments', 1]];
    }

    /**
     * @throws Exception
     */
    public function handleArguments(ControllerArgumentsEvent $event): void
    {
        $arguments = $event->getArguments();
        foreach ($arguments as $argument) {
            if ($argument instanceof AbstractValidatedRequest === false) {
                continue;
            }

            $result = $argument->validate();
            if ($result instanceof Response) {
                $event->setController(fn() => $result);
                break;
            }
        }
    }
}
