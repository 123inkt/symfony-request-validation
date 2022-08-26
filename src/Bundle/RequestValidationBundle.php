<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Bundle;

use DigitalRevolution\SymfonyRequestValidation\DependencyInjection\RequestValidationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RequestValidationBundle extends Bundle
{
    protected function getContainerExtensionClass(): string
    {
        return RequestValidationExtension::class;
    }
}
