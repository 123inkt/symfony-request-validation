<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\DependencyInjection;

use DigitalRevolution\SymfonyRequestValidation\DependencyInjection\RequestValidationExtension;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\DependencyInjection\RequestValidationExtension
 * @covers ::__construct
 */
class RequestValidationExtensionTest extends TestCase
{
    /**
     * @covers ::load
     * @throws Exception
     */
    public function testLoad(): void
    {
        $fileLocator = $this->createMock(FileLocatorInterface::class);
        $fileLocator->expects(static::once())->method('locate')->willReturn(dirname(__DIR__, 3) . '/src/Resources/config/services.php');
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects(static::once())->method('fileExists')->willReturn(true);

        $extension = new RequestValidationExtension($fileLocator);
        $extension->load([], $container);
    }
}
