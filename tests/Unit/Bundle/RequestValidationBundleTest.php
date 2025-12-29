<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Bundle;

use DigitalRevolution\SymfonyRequestValidation\Bundle\RequestValidationBundle;
use DigitalRevolution\SymfonyRequestValidation\DependencyInjection\RequestValidationExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

#[CoversClass(RequestValidationBundle::class)]
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
