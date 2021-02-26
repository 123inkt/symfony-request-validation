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

    public function __construct(ConstraintFactory $factory = null)
    {
        $this->factory = $factory ?? new ConstraintFactory();
    }

    /**
     * @throws InvalidRuleException
     */
    public function createConstraint(ValidationRules $validationRules): RequestConstraint
    {
        $options = [];
        foreach ($validationRules->getDefinitions() as $key => $definitions) {
            $options[$key] = $this->factory->fromRuleDefinitions($definitions);
        }

        return new RequestConstraint($options);
    }
}
