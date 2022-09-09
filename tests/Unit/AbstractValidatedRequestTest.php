<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest
 * @covers ::__construct
 */
class AbstractValidatedRequestTest extends TestCase
{
    /** @var RequestConstraintFactory&MockObject */
    private RequestConstraintFactory $constraintFactory;
    /** @var ValidatorInterface&MockObject */
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constraintFactory = $this->createMock(RequestConstraintFactory::class);
        $this->validator         = $this->createMock(ValidatorInterface::class);
    }

    /**
     * @covers ::__construct
     * @throws Exception
     */
    public function testConstructorNullRequest(): void
    {
        $stack = new RequestStack();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Request is 'null', unable to validate");
        new MockValidatedRequest($stack, $this->validator, $this->constraintFactory);
    }

    /**
     * @covers ::validate
     * @covers ::isValid
     * @covers ::getRequest
     * @throws Exception
     */
    public function testValidateWithoutViolations(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        $rules = new ValidationRules([]);

        $this->validator->expects(self::once())->method('validate')->willReturn(new ConstraintViolationList());

        $validatedRequest = new MockValidatedRequest($stack, $this->validator, $this->constraintFactory, $rules);
        static::assertNull($validatedRequest->validate());
        static::assertTrue($validatedRequest->isValid());
        static::assertSame($request, $validatedRequest->getRequest());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     * @covers ::handleViolations
     * @throws BadRequestException|InvalidRuleException
     */
    public function testValidateWithViolations(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        // create rules
        $constraint = new Collection(['fields' => ['test' => new NotBlank()]]);
        $rules      = new ValidationRules(['request' => $constraint]);

        // create violations
        $violations = new ConstraintViolationList();
        $violations->add($this->createMock(ConstraintViolation::class));

        // create validator
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->willReturn($violations);

        $validatedRequest = new MockValidatedRequest($stack, $this->validator, $this->constraintFactory, $rules);
        $this->expectException(BadRequestException::class);
        $validatedRequest->validate();
    }

    /**
     * @covers ::validate
     * @covers ::isValid
     * @throws Exception
     */
    public function testValidateWithoutValidationRules(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        $this->validator->expects(self::never())->method('validate');

        $validatedRequest = new MockValidatedRequest($stack, $this->validator, $this->constraintFactory, null);

        static::assertNull($validatedRequest->validate());
        static::assertTrue($validatedRequest->isValid());
    }

    /**
     * @covers ::validate
     * @covers ::isValid
     * @throws Exception
     */
    public function testValidateWithCustomValidation(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);
        $response = new Response();

        $rules = new ValidationRules([]);

        $this->validator->expects(self::once())->method('validate')->willReturn(new ConstraintViolationList());

        $validatedRequest = new MockValidatedRequest($stack, $this->validator, $this->constraintFactory, $rules);
        $validatedRequest->setValidateCustomRulesResult($response);

        static::assertSame($response, $validatedRequest->validate());
        static::assertFalse($validatedRequest->isValid());
    }
}
