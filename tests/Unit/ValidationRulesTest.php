<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\ValidationRules
 */
class ValidationRulesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getQueryRules
     * @covers ::getRequestRules
     */
    public function testConstructorAndGetters(): void
    {
        $constraintA = new NotBlank();
        $constraintB = new NotNull();
        $rules       = new ValidationRules(['query' => $constraintA]);
        static::assertSame($constraintA, $rules->getQueryRules());
        static::assertNull($rules->getRequestRules());

        $rules = new ValidationRules(['query' => $constraintA, 'request' => $constraintB]);
        static::assertSame($constraintA, $rules->getQueryRules());
        static::assertSame($constraintB, $rules->getRequestRules());
    }

    /**
     * @covers ::__construct
     */
    public function testInvalidPropertyArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ValidationRules(['query' => 'a', 'b']);
    }
}
