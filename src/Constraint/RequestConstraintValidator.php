<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use Symfony\Component\HttpFoundation\Exception\JsonException;
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
        $this->validateAttributes($constraint, $value);
    }

    private function validateQuery(RequestConstraint $constraint, Request $request): void
    {
        if ($constraint->query !== null) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('[query]')
                ->validate($request->query->all(), $constraint->query);
        } elseif ($constraint->allowExtraFields === false && count($request->query) > 0) {
            $this->context->buildViolation($constraint->queryMessage)
                ->atPath('[query]')
                ->setCode($constraint::MISSING_QUERY_CONSTRAINT)
                ->addViolation();
        }
    }

    private function validateRequest(RequestConstraint $constraint, Request $request): void
    {
        if ($constraint->request === null) {
            if ($constraint->allowExtraFields === false && count($request->request) > 0) {
                $this->context->buildViolation($constraint->requestMessage)
                    ->atPath('[request]')
                    ->setCode($constraint::MISSING_REQUEST_CONSTRAINT)
                    ->addViolation();
            }

            return;
        }

        if (in_array($request->getContentType(), ['json', 'jsonld'], true)) {
            $data = $this->validateAndGetJsonBody($constraint, $request);
            if ($data === null) {
                return;
            }
        } else {
            $data = $request->request->all();
        }

        $this->context->getValidator()
            ->inContext($this->context)
            ->atPath('[request]')
            ->validate($data, $constraint->request);
    }

    /**
     * @return mixed[]|null
     */
    private function validateAndGetJsonBody(RequestConstraint $constraint, Request $request): ?array
    {
        try {
            return $request->toArray();
        } catch (JsonException $exception) {
            $this->context->buildViolation($constraint->invalidBodyMessage)
                ->atPath('[request]')
                ->setCode($constraint::INVALID_BODY_CONTENT)
                ->addViolation();

            return null;
        }
    }

    private function validateAttributes(RequestConstraint $constraint, Request $request): void
    {
        if ($constraint->attributes !== null) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('[attributes]')
                ->validate($request->attributes->all(), $constraint->attributes);
        }
    }
}
