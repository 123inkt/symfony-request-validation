<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

#[CoversClass(ValidationRules::class)]
class ValidationRulesTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $definitions = ['query' => new NotBlank()];
        $rules       = new ValidationRules($definitions);
        static::assertSame($definitions, $rules->getDefinitions());
    }

    public function testConstructorArrayOfConstraints(): void
    {
        $definitions = ['query' => [new Required(), new NotBlank()]];
        $rules       = new ValidationRules($definitions);
        static::assertSame($definitions, $rules->getDefinitions());
    }

    public function testInvalidPropertyArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ValidationRules(['query' => 'a', 'b']);
    }

    public function testGetAllowExtraFields(): void
    {
        $rules = new ValidationRules(['query' => 'a']);
        static::assertFalse($rules->getAllowExtraFields());

        $rules = new ValidationRules(['query' => 'a'], true);
        static::assertTrue($rules->getAllowExtraFields());
    }
}
