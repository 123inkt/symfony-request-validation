<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Mock;

use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MockValidatedRequest extends AbstractValidatedRequest
{
    private ?ValidationRules $rules;
    /** @var Response|Exception|null */
    private $result = null;

    public function __construct(
        RequestStack $requestStack,
        ValidatorInterface $validator,
        RequestConstraintFactory $constraintFactory,
        ?ValidationRules $rules = null
    ) {
        $this->rules = $rules;
        parent::__construct($requestStack, $validator, $constraintFactory);
    }

    /**
     * @inheritDoc
     */
    protected function getValidationRules(): ?ValidationRules
    {
        return $this->rules;
    }

    /**
     * @param Response|Exception|null $result
     */
    public function setValidateCustomRulesResult($result): void
    {
        $this->result = $result;
    }

    /**
     * Upgrade protected to public
     * @throws Exception
     */
    public function validateCustomRules(): ?Response
    {
        if ($this->result instanceof Exception) {
            throw $this->result;
        }

        return $this->result;
    }
}
