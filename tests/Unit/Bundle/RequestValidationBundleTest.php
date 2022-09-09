<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Bundle;

use DigitalRevolution\SymfonyRequestValidation\Bundle\RequestValidationBundle;
use DigitalRevolution\SymfonyRequestValidation\DependencyInjection\RequestValidationExtension;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Bundle\RequestValidationBundle
 */
class RequestValidationBundleTest extends TestCase
{
    /**
     * @covers ::getContainerExtensionClass
     * @throws ReflectionException
     */
    public function testGetContainerExtensionClass(): void
    {
        $bundle = new RequestValidationBundle();

        $class  = new ReflectionClass($bundle);
        $method = $class->getMethod('getContainerExtensionClass');
        $method->setAccessible(true);
        $result = $method->invoke($bundle, []);

        static::assertSame(RequestValidationExtension::class, $result);
    }
}
