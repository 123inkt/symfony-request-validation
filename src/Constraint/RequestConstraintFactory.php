<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;

class RequestConstraintFactory
{
    /** @var ConstraintFactory */
    private $factory;

    public function __construct(ConstraintFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @throws InvalidRuleException
     */
    public function createConstraint(ValidationRules $validationRules): RequestConstraint
    {
        $options            = [];
        $queryDefinitions   = $validationRules->getQueryRules();
        $requestDefinitions = $validationRules->getRequestRules();

        if ($queryDefinitions !== null) {
            $options['query'] = $this->factory->fromRuleDefinitions($queryDefinitions);
        }
        if ($requestDefinitions !== null) {
            $options['request'] = $this->factory->fromRuleDefinitions($requestDefinitions);
        }

        return new RequestConstraint($options);
    }
}
