<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Symfony\Component\Validator\Constraint;

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
        /**
         * @var array{
         *     query?: Constraint|Constraint[],
         *     request?: Constraint|Constraint[],
         *     attributes?: Constraint|Constraint[],
         * } $options
         */
        $options = [];
        foreach ($validationRules->getDefinitions() as $key => $definitions) {
            $options[$key] = $this->factory->fromRuleDefinitions($definitions, $validationRules->getAllowExtraFields());
        }

        // Set extra constraint options
        $options['allowExtraFields'] = $validationRules->getAllowExtraFields();

        return new RequestConstraint($options);
    }
}
