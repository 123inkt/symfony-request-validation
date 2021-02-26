<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintValidator
 */
class RequestConstraintValidatorTest extends TestCase
{
    /** @var ExecutionContext */
    private $context;

    /** @var RequestConstraintValidator */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock->method('trans')->willReturn('');

        $this->validator = new RequestConstraintValidator();
        $this->context   = new ExecutionContext(Validation::createValidator(), 'root', $translatorMock);
        $this->validator->initialize($this->context);
    }

    /**
     * @covers ::validate
     */
    public function testValidateUnexpectedTypeException(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Assert\NotBlank());
    }

    /**
     * @param array<mixed> $data
     * @dataProvider dataProvider
     * @covers ::validate
     */
    public function testValidateQuery(array $data, bool $success): void
    {
        $request    = new Request($data);
        $constraint = new RequestConstraint(['query' => new Assert\Collection(['email' => new Assert\Required(new Assert\Email())])]);
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        static::assertCount($success ? 0 : 1, $this->context->getViolations());
    }

    /**
     * @param array<mixed> $data
     * @dataProvider dataProvider
     * @covers ::validate
     */
    public function testValidateRequest(array $data, bool $success): void
    {
        $request    = new Request([], $data);
        $constraint = new RequestConstraint(['request' => new Assert\Collection(['email' => new Assert\Required(new Assert\Email())])]);
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        static::assertCount($success ? 0 : 1, $this->context->getViolations());
    }


    /**
     * @param array<mixed> $data
     * @dataProvider dataProvider
     * @covers ::validate
     */
    public function testValidateAttributes(array $data, bool $success): void
    {
        $request    = new Request([], [], $data);
        $constraint = new RequestConstraint(['attributes' => new Assert\Collection(['email' => new Assert\Required(new Assert\Email())])]);
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        static::assertCount($success ? 0 : 1, $this->context->getViolations());
    }

    /**
     * @param array<mixed> $data
     * @dataProvider dataProvider
     * @covers ::validate
     */
    public function testValidateQueryRequestAttributes(array $data, bool $success): void
    {
        $request    = new Request($data, $data, $data);
        $constraint = new RequestConstraint([
            'query'      => new Assert\Collection(['email' => new Assert\Required(new Assert\Email())]),
            'request'    => new Assert\Collection(['email' => new Assert\Required(new Assert\Email())]),
            'attributes' => new Assert\Collection(['email' => new Assert\Required(new Assert\Email())])
        ]);
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        static::assertCount($success ? 0 : 3, $this->context->getViolations());
    }

    /**
     * @return array<string, array<array, bool>>
     */
    public function dataProvider(): array
    {
        return [
            'success' => [['email' => 'example@example.com'], true],
            'failure' => [['email' => 'unit test'], false]
        ];
    }

    /**
     * Test that 'null' request should be ignored
     *
     * @covers ::validate
     */
    public function testValidateNullRequest(): void
    {
        $request    = null;
        $constraint = new RequestConstraint();
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    /**
     * Test that 'null' request should be ignored
     *
     * @covers ::validate
     */
    public function testValidateWrongTypeViolation(): void
    {
        $request    = 5;
        $constraint = new RequestConstraint();
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        $violations = $this->context->getViolations();
        static::assertCount(1, $violations);
        static::assertSame($constraint->wrongTypeMessage, $violations->get(0)->getMessageTemplate());
    }

    /**
     * Test that if no constraints have been specified. the request query _must_ be empty
     *
     * @covers ::validate
     */
    public function testValidateEmptyConstraintsFilledQuery(): void
    {
        $request    = new Request(['a']);
        $constraint = new RequestConstraint();
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        $violations = $this->context->getViolations();
        static::assertCount(1, $violations);
        static::assertSame($constraint->queryMessage, $violations->get(0)->getMessageTemplate());
    }

    /**
     * Test that if no constraints have been specified. the request request _must_ be empty
     *
     * @covers ::validate
     */
    public function testValidateEmptyConstraintsFilledRequest(): void
    {
        $request    = new Request([], ['b']);
        $constraint = new RequestConstraint();
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        $violations = $this->context->getViolations();
        static::assertCount(1, $violations);
        static::assertSame($constraint->requestMessage, $violations->get(0)->getMessageTemplate());
    }

    /**
     * @covers ::validate
     */
    public function testValidateQueryFailure(): void
    {
        $request    = new Request(['email' => 'example@example.com']);
        $constraint = new RequestConstraint(['query' => new Assert\Collection(['email' => new Assert\Required(new Assert\Email())])]);
        $this->context->setConstraint($constraint);
        $this->validator->validate($request, $constraint);
        static::assertCount(0, $this->context->getViolations());
    }
}
