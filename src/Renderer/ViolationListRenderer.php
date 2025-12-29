<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Renderer;

use DigitalRevolution\SymfonyRequestValidation\Utility\PropertyPath;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationListRenderer
{
    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $violations
     */
    public function __construct(private readonly ConstraintViolationListInterface $violations)
    {
    }

    public function render(): string
    {
        $messages = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($this->violations as $violation) {
            $propertyPath = implode('.', PropertyPath::toArray($violation->getPropertyPath()));
            $messages[]   = $propertyPath . ': ' . $violation->getMessage();
        }

        return implode("\n", $messages);
    }
}
