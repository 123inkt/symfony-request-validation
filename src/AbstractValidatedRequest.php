<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\Renderer\ViolationListRenderer;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    protected Request                  $request;
    protected ValidatorInterface       $validator;
    protected RequestConstraintFactory $constraintFactory;
    protected bool                     $isValid = false;

    /**
     * @throws BadRequestException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator, RequestConstraintFactory $constraintFactory)
    {
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            throw new BadRequestException("Request is 'null', unable to validate");
        }

        $this->request           = $request;
        $this->validator         = $validator;
        $this->constraintFactory = $constraintFactory;
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
     * @throws BadRequestException|InvalidRuleException
     * @internal invoked by RequestValidationSubscriber
     */
    final public function validate(): ?Response
    {
        $rules = $this->getValidationRules();
        if ($rules !== null) {
            $violationList = $this->validator->validate($this->request, $this->constraintFactory->createConstraint($rules));
            if (count($violationList) > 0) {
                return $this->handleViolations($violationList);
            }
        }

        $response = $this->validateCustomRules();
        if ($response instanceof Response) {
            return $response;
        }

        $this->isValid = true;

        return null;
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getValidationRules(): ?ValidationRules;

    /**
     * Override this function to validate addition custom validation rules after the standard Symfony rules have been validated.
     * - return null if validation was successful
     * - return Response to immediately end the request with this response.
     * - throw BadRequestException when request was not valid.
     * @throws BadRequestException
     * @codeCoverageIgnore
     */
    protected function validateCustomRules(): ?Response
    {
        return null;
    }

    /**
     * Called when there are one or more violations. Defaults to throwing BadRequestException. Overwrite
     * to add your own handling. If response is returned, this response will be sent instead of invoking the controller.
     *
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $violationList
     * @throws BadRequestException
     */
    protected function handleViolations(ConstraintViolationListInterface $violationList): ?Response
    {
        throw new BadRequestException((new ViolationListRenderer($violationList))->render());
    }
}
