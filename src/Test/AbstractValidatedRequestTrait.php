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
     * @param class-string<V>                                                                $classString
     * @param (callable(RequestStack, ValidatorInterface, RequestConstraintFactory): V)|null $constructor
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
        $stack = new RequestStack();
        $stack->push($request ?? new Request());

        $validatedRequest = new $classString(
            $stack,
            $validator ?? Validation::createValidator(),
            $constraintFactory ?? new RequestConstraintFactory(),
            ...$arguments
        );
        $validatedRequest->validate();

        return $validatedRequest;
    }
}
