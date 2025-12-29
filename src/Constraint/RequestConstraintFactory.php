<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;

class RequestConstraintFactory
{
    public function __construct(private readonly ConstraintFactory $factory = new ConstraintFactory())
    {
    }

    /**
     * @throws InvalidRuleException
     */
    public function createConstraint(ValidationRules $validationRules): RequestConstraint
    {
        $query = $request = $attributes = null;

        $definitions = $validationRules->getDefinitions();
        if (isset($definitions['query'])) {
            $query = $this->factory->fromRuleDefinitions($definitions['query'], $validationRules->getAllowExtraFields());
        }
        if (isset($definitions['request'])) {
            $request = $this->factory->fromRuleDefinitions($definitions['request'], $validationRules->getAllowExtraFields());
        }
        if (isset($definitions['attributes'])) {
            $attributes = $this->factory->fromRuleDefinitions($definitions['attributes'], $validationRules->getAllowExtraFields());
        }

        return new RequestConstraint($query, $request, $attributes, $validationRules->getAllowExtraFields());
    }
}
