<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var Request */
    protected $request;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var Constraint */
    protected $constraint;

    /** @var bool */
    protected $isValid;

    /**
     * @throws InvalidRuleException
     * @throws InvalidRequestException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            throw new InvalidRequestException("Request is 'null', unable to validate");
        }

        $this->request    = $request;
        $this->validator  = $validator;
        $this->constraint = (new RequestConstraintFactory(new ConstraintFactory()))->createConstraint($this->getValidationRules());
        $this->isValid    = $this->validate();
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getValidationRules(): ValidationRules;

    /**
     * Called when there are one or more violations. Defaults to throwing RequestValidationException. Overwrite
     * to add your own handling
     *
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $violationList
     * @throws InvalidRequestException
     */
    protected function handleViolations(ConstraintViolationListInterface $violationList): void
    {
        $messages = [];
        foreach ($violationList as $violation) {
            $messages[] = $violation->getMessage();
        }

        throw new InvalidRequestException(implode("\n", $messages));
    }

    /**
     * @throws InvalidRequestException
     */
    protected function validate(): bool
    {
        $violationList = $this->validator->validate($this->request, $this->constraint);
        if (count($violationList) > 0) {
            $this->handleViolations($violationList);
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }
}
