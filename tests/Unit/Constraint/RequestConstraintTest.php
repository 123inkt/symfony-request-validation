<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

#[CoversClass(RequestConstraint::class)]
class RequestConstraintTest extends TestCase
{
    public function testConstructDefaultOptions(): void
    {
        $constraint = new RequestConstraint();
        static::assertNull($constraint->query);
        static::assertNull($constraint->request);
        static::assertNull($constraint->attributes);
        static::assertFalse($constraint->allowExtraFields);
    }

    public function testConstructConstraintOptions(): void
    {
        $constraintA = new NotBlank();
        $constraintB = new NotNull();
        $constraintC = new IsNull();
        $constraint  = new RequestConstraint(
            ['query' => $constraintA, 'request' => $constraintB, 'attributes' => $constraintC, 'allowExtraFields' => true]
        );
        static::assertSame($constraintA, $constraint->query);
        static::assertSame($constraintB, $constraint->request);
        static::assertSame($constraintC, $constraint->attributes);
        static::assertTrue($constraint->allowExtraFields);
    }

    public function testConstructIncorrectOption(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new RequestConstraint(['invalid' => 'invalid']);
    }

    public function testGetRequiredOptions(): void
    {
        $constraint = new RequestConstraint();
        static::assertSame(['query', 'request', 'attributes', 'allowExtraFields'], $constraint->getRequiredOptions());
    }
}
