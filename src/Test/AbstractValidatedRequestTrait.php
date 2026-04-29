<?php

declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Test;

use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @codeCoverageIgnore Test purposely only
 */
trait AbstractValidatedRequestTrait
{
    /**
     * @template V of AbstractValidatedRequest
     * @param class-string<V>                                                         $classString
     * @param callable(RequestStack, ValidatorInterface, RequestConstraintFactory): V $constructor
     *
     * @return V
     * @throws InvalidRuleException
     */
    final protected static function createValidatedRequest(
        string $classString,
        ?Request $request = null,
        ?ValidatorInterface $validator = null,
        ?RequestConstraintFactory $constraintFactory = null,
        ?callable $constructor = null,
    ): AbstractValidatedRequest {
        $stack = new RequestStack();
        $stack->push($request ?? new Request());

        if ($constructor === null) {
            $validatedRequest = new $classString(
                $stack,
                $validator ?? Validation::createValidator(),
                $constraintFactory ?? new RequestConstraintFactory(),
            );
        } else {
            $validatedRequest = $constructor(
                $stack,
                $validator ?? Validation::createValidator(),
                $constraintFactory ?? new RequestConstraintFactory(),
            );
        }
        $validatedRequest->validate();

        return $validatedRequest;
    }
}
