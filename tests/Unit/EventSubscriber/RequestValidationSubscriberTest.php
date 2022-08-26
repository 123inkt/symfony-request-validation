<?php
declare(strict_types=1);

namespace EventSubscriber;

use DigitalRevolution\SymfonyRequestValidation\EventSubscriber\RequestValidationSubscriber;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\EventSubscriber\RequestValidationSubscriber
 * @covers ::__construct
 */
class RequestValidationSubscriberTest extends TestCase
{
    /** @var HttpKernelInterface&MockObject */
    private HttpKernelInterface         $kernel;
    private Request                     $request;
    private RequestValidationSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->kernel     = $this->createMock(HttpKernelInterface::class);
        $this->request    = new Request();
        $this->subscriber = new RequestValidationSubscriber();
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
        $request = $this->createMock(MockValidatedRequest::class);
        $request->expects(self::once())->method('validate')->willReturn(null);

        try {
            $this->subscriber->handleArguments($this->createArgumentsEvent([$request]));
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
        $request = $this->createMock(MockValidatedRequest::class);
        $request->expects(self::once())->method('validate')->willReturn(new InvalidArgumentException('foobar'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('foobar');
        $this->subscriber->handleArguments($this->createArgumentsEvent([$request]));
    }

    /**
     * @covers ::handleArguments
     * @throws Exception
     */
    public function testHandleArgumentsShouldChangeResponse(): void
    {
        $response = new Response();

        $request = $this->createMock(MockValidatedRequest::class);
        $request->expects(self::once())->method('validate')->willReturn($response);

        $event = $this->createArgumentsEvent([$request]);
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
