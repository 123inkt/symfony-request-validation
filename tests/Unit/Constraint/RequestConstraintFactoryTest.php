<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

#[CoversClass(RequestConstraintFactory::class)]
class RequestConstraintFactoryTest extends TestCase
{
    /**
     * @throws InvalidRuleException
     */
    public function testCreateRequestConstraint(): void
    {
        $factory = new RequestConstraintFactory(new ConstraintFactory());

        // without any rules
        $result = $factory->createConstraint(new ValidationRules([]));
        static::assertEquals(new RequestConstraint(), $result);

        $constraintA = new Assert\NotNull();
        $constraintB = new Assert\NotBlank();
        $result      = $factory->createConstraint(new ValidationRules(['query' => $constraintA, 'request' => $constraintB]));
        static::assertEquals(new RequestConstraint(['query' => $constraintA, 'request' => $constraintB]), $result);
    }

    /**
     * @throws InvalidRuleException
     */
    public function testCreateRequestConstraintAllowExtraFields(): void
    {
        $factory = new RequestConstraintFactory(new ConstraintFactory());

        // without any rules
        $result = $factory->createConstraint(new ValidationRules([], true));
        static::assertEquals(new RequestConstraint(['allowExtraFields' => true]), $result);

        $constraintA = new Assert\NotNull();
        $constraintB = new Assert\NotBlank();
        $result      = $factory->createConstraint(new ValidationRules(['query' => $constraintA, 'request' => $constraintB], true));
        static::assertEquals(new RequestConstraint(['query' => $constraintA, 'request' => $constraintB, 'allowExtraFields' => true]), $result);
    }
}
