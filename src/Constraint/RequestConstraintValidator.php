<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RequestConstraintValidator extends ConstraintValidator
{
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

        if ($constraint->query !== null) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('[query]')
                ->validate($value->query->all(), $constraint->query);
        } elseif (count($value->query) > 0) {
            $context->buildViolation($constraint->queryMessage)
                ->atPath('[query]')
                ->setCode($constraint::MISSING_QUERY_CONSTRAINT)
                ->addViolation();
        }

        if ($constraint->request !== null) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('[request]')
                ->validate($value->request->all(), $constraint->request);
        } elseif (count($value->request) > 0) {
            $context->buildViolation($constraint->requestMessage)
                ->atPath('[request]')
                ->setCode($constraint::MISSING_REQUEST_CONSTRAINT)
                ->addViolation();
        }

        if ($constraint->attributes !== null) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('[attributes]')
                ->validate($value->attributes->all(), $constraint->attributes);
        }
    }
}
