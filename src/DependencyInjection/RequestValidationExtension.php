<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * Automatically registered to symfony service via RequestValidationBundle
 */
class RequestValidationExtension extends Extension
{
    private ?FileLocatorInterface $fileLocator;

    public function __construct(?FileLocatorInterface $fileLocator = null)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, $this->fileLocator ?? new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');
    }
}
