<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Renderer;

use DigitalRevolution\SymfonyRequestValidation\Utility\PropertyPath;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationListRenderer
{
    /** @var ConstraintViolationListInterface */
    private $violationList;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violationList = $violations;
    }

    public function render(): string
    {
        $messages = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($this->violationList as $violation) {
            $propertyPath = implode('.', PropertyPath::toArray($violation->getPropertyPath()));
            $messages[]   = $propertyPath . ': ' . $violation->getMessage();
        }

        return implode("\n", $messages);
    }
}
