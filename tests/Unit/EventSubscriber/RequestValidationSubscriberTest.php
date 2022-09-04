<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\EventSubscriber;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RequestConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\EventSubscriber\RequestValidationSubscriber;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\EventSubscriber\RequestValidationSubscriber
 */
class RequestValidationSubscriberTest extends TestCase
{
    /** @var HttpKernelInterface&MockObject */
    private HttpKernelInterface         $kernel;
    private MockValidatedRequest        $validatedRequest;
    private Request                     $request;
    private RequestValidationSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel     = $this->createMock(HttpKernelInterface::class);
        $this->subscriber = new RequestValidationSubscriber();
        $this->request    = new Request();

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $stack = new RequestStack();
        $stack->push($this->request);
        $this->validatedRequest = new MockValidatedRequest(
            $stack,
            $validator,
            $this->createMock(RequestConstraintFactory::class),
            new ValidationRules([])
        );
    }

    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        static::assertSame([KernelEvents::CONTROLLER_ARGUMENTS => ['handleArguments', 1]], RequestValidationSubscriber::getSubscribedEvents());
    }

    /**
     * @covers ::handleArguments
     */
    public function testHandleArgumentsShouldOnlyAcceptAbstractValidatedRequest(): void
    {
        try {
            $this->subscriber->handleArguments($this->createArgumentsEvent([1, 'foo', new stdClass(), 1.1, null]));
            $success = true;
        } catch (Exception $e) {
            $success = false;
        }
        static::assertTrue($success);
    }

    /**
     * @covers ::handleArguments
     */
    public function testHandleArgumentsAbstractValidatedRequestShouldPass(): void
    {
        $this->validatedRequest->setValidateCustomRulesResult(null);

        try {
            $this->subscriber->handleArguments($this->createArgumentsEvent([$this->validatedRequest]));
            $success = true;
        } catch (Exception $e) {
            $success = false;
        }
        static::assertTrue($success);
    }

    /**
     * @covers ::handleArguments
     * @throws Exception
     */
    public function testHandleArgumentsShouldThrowException(): void
    {
        $this->validatedRequest->setValidateCustomRulesResult(new BadRequestException('foobar'));

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('foobar');
        $this->subscriber->handleArguments($this->createArgumentsEvent([$this->validatedRequest]));
    }

    /**
     * @covers ::handleArguments
     * @throws Exception
     */
    public function testHandleArgumentsShouldChangeResponse(): void
    {
        $response = new Response();

        $this->validatedRequest->setValidateCustomRulesResult($response);

        $event = $this->createArgumentsEvent([$this->validatedRequest]);
        $this->subscriber->handleArguments($event);

        static::assertSame($response, ($event->getController())());
    }

    /**
     * @param array<int|string|float|object|null> $arguments
     */
    private function createArgumentsEvent(array $arguments): ControllerArgumentsEvent
    {
        return new ControllerArgumentsEvent($this->kernel, static fn() => true, $arguments, $this->request, 1);
    }
}
