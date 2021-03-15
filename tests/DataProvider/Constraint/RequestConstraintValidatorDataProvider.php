<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\DataProvider\Constraint;

class RequestConstraintValidatorDataProvider
{
    /**
     * @return array<string, array<array, bool>>
     */
    public static function dataProvider(): array
    {
        return [
            'success' => [['email' => 'example@example.com'], true],
            'failure' => [['email' => 'unit test'], false]
        ];
    }
}
