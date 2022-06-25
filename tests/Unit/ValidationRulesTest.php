<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\ValidationRules
 */
class ValidationRulesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getDefinitions
     */
    public function testConstructorAndGetters(): void
    {
        $definitions = ['query' => new NotBlank()];
        $rules       = new ValidationRules($definitions);
        static::assertSame($definitions, $rules->getDefinitions());
    }

    /**
     * @covers ::__construct
     */
    public function testInvalidPropertyArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ValidationRules(['query' => 'a', 'b']);
    }

    /**
     * @covers ::getAllowExtraFields
     */
    public function testGetAllowExtraFields(): void
    {
        $rules = new ValidationRules(['query' => 'a']);
        static::assertFalse($rules->getAllowExtraFields());

        $rules = new ValidationRules(['query' => 'a'], true);
        static::assertTrue($rules->getAllowExtraFields());
    }
}
