<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraint;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraint
 */
class RequestConstraintTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructDefaultOptions(): void
    {
        $constraint = new RequestConstraint();
        static::assertNull($constraint->query);
        static::assertNull($constraint->request);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructConstraintOptions(): void
    {
        $constraintA = new NotBlank();
        $constraintB = new NotNull();
        $constraint  = new RequestConstraint(['query' => $constraintA, 'request' => $constraintB]);
        static::assertSame($constraintA, $constraint->query);
        static::assertSame($constraintB, $constraint->request);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructIncorrectOption(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new RequestConstraint(['invalid' => 'invalid']);
    }

    /**
     * @covers ::getRequiredOptions
     */
    public function testGetRequiredOptions(): void
    {
        $constraint = new RequestConstraint();
        static::assertSame(['query', 'request', 'attributes'], $constraint->getRequiredOptions());
    }
}
