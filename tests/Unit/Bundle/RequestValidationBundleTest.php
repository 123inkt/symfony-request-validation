<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Bundle;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use DigitalRevolution\SymfonyRequestValidation\Bundle\RequestValidationBundle;
use DigitalRevolution\SymfonyRequestValidation\DependencyInjection\RequestValidationExtension;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

#[CoversClass(RequestValidationBundle::class)]
#[CoversFunction('getContainerExtensionClass')]
class RequestValidationBundleTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testGetContainerExtensionClass(): void
    {
        $bundle = new RequestValidationBundle();

        $class  = new ReflectionClass($bundle);
        $method = $class->getMethod('getContainerExtensionClass');
        $result = $method->invoke($bundle, []);

        static::assertSame(RequestValidationExtension::class, $result);
    }
}
