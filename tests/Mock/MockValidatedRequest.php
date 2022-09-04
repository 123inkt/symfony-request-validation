<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Mock;

use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MockValidatedRequest extends AbstractValidatedRequest
{
    private ?ValidationRules $rules    = null;
    private ?Response        $response = null;

    public function __construct(
        RequestStack             $requestStack,
        ValidatorInterface       $validator,
        RequestConstraintFactory $constraintFactory,
        ValidationRules          $rules = null
    ) {
        $this->rules = $rules;
        parent::__construct($requestStack, $validator, $constraintFactory);
    }

    /**
     * @inheritDoc
     */
    protected function getValidationRules(): ValidationRules
    {
        if ($this->rules === null) {
            throw new RuntimeException('ValidationRules not set');
        }
        return $this->rules;
    }

    public function setValidateCustomRulesResult(?Response $response): void
    {
        $this->response = $response;
    }

    /**
     * Upgrade protected to public
     */
    public function validateCustomRules(): ?Response
    {
        return $this->response;
    }
}
