<?php

declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Test;

use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

/**
 * @codeCoverageIgnore For test purposes only
 */
trait AbstractValidatedRequestTrait
{
    /**
     * @template V of AbstractValidatedRequest
     * @param class-string<V> $classString
     *
     * @return V
     * @throws Throwable
     */
    final protected static function createValidatedRequest(
        string $classString,
        ?Request $request = null,
        ?ValidatorInterface $validator = null,
        ?RequestConstraintFactory $constraintFactory = null,
        mixed ...$arguments
    ): AbstractValidatedRequest {
        return new $classString(
            new RequestStack([$request ?? new Request()]),
            $validator ?? Validation::createValidator(),
            $constraintFactory ?? new RequestConstraintFactory(),
            ...$arguments
        );
    }
}
