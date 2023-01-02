<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RequestConstraintValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     *
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof RequestConstraint === false) {
            throw new UnexpectedTypeException($constraint, RequestConstraint::class);
        }

        if ($value === null) {
            return;
        }

        $context = $this->context;
        if ($value instanceof Request === false) {
            $context->buildViolation($constraint->wrongTypeMessage)
                ->setCode($constraint::WRONG_VALUE_TYPE)
                ->addViolation();

            return;
        }

        $this->validateQuery($constraint, $value);
        $this->validateRequest($constraint, $value);
        $this->validateJson($constraint, $value);
        $this->validateAttributes($constraint, $value);
    }

    private function validateQuery(RequestConstraint $constraint, Request $value): void
    {
        if ($constraint->query !== null) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('[query]')
                ->validate($value->query->all(), $constraint->query);
        } elseif ($constraint->allowExtraFields === false && count($value->query) > 0) {
            $this->context->buildViolation($constraint->queryMessage)
                ->atPath('[query]')
                ->setCode($constraint::MISSING_QUERY_CONSTRAINT)
                ->addViolation();
        }
    }

    private function validateRequest(RequestConstraint $constraint, Request $value): void
    {
        if ($constraint->request !== null) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('[request]')
                ->validate($value->request->all(), $constraint->request);
        } elseif ($constraint->allowExtraFields === false && count($value->request) > 0) {
            $this->context->buildViolation($constraint->requestMessage)
                ->atPath('[request]')
                ->setCode($constraint::MISSING_REQUEST_CONSTRAINT)
                ->addViolation();
        }
    }

    private function validateJson(RequestConstraint $constraint, Request $value): void
    {
        if ($constraint->json === null) {
            return;
        }
        $content = $value->getContent();
        try {
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->context->addViolation('The body is not valid json');

            return;
        }

        $this->context->getValidator()->inContext($this->context)->atPath('[json]')->validate($json, $constraint->json);
    }

    private function validateAttributes(RequestConstraint $constraint, Request $value): void
    {
        if ($constraint->attributes !== null) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('[attributes]')
                ->validate($value->attributes->all(), $constraint->attributes);
        }
    }
}
