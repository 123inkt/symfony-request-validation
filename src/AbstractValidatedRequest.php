<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\Renderer\ViolationListRenderer;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    protected Request            $request;
    protected ValidatorInterface $validator;
    protected Constraint         $constraint;
    protected bool               $isValid = false;

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
     * @return Exception|Response|null
     * @internal invoked by RequestValidationSubscriber
     */
    public function validate()
    {
        $violationList = $this->validator->validate($this->request, $this->constraint);
        if (count($violationList) > 0) {
            return $this->handleViolations($violationList);
        }
        $this->isValid = true;

        return null;
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getValidationRules(): ValidationRules;

    /**
     * Called when there are one or more violations. Defaults to throwing RequestValidationException. Overwrite
     * to add your own handling. If response is returned, this response will be send instead of invoking the controller.
     *
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $violationList
     *
     * @return Exception|Response
     */
    protected function handleViolations(ConstraintViolationListInterface $violationList)
    {
        return new InvalidRequestException((new ViolationListRenderer($violationList))->render());
    }
}
