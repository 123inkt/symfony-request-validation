<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
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

#[CoversClass(AbstractValidatedRequest::class)]
class AbstractValidatedRequestTest extends TestCase
{
    private RequestConstraintFactory&MockObject $constraintFactory;
    private ValidatorInterface&MockObject $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constraintFactory = $this->createMock(RequestConstraintFactory::class);
        $this->validator         = $this->createMock(ValidatorInterface::class);
    }

    /**
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
