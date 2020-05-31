<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Mock;

use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MockValidatedRequest extends AbstractValidatedRequest
{
    /** @var ValidationRules|null */
    private $rules;

    public function __construct(RequestStack $requestStack, ValidatorInterface $validator, ValidationRules $rules = null)
    {
        $this->rules = $rules;
        parent::__construct($requestStack, $validator);
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
}
