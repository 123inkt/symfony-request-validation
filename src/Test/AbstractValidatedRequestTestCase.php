<?php

declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Test;

use PHPUnit\Framework\TestCase;

/**
 * @codeCoverageIgnore Test purposely only
 */
abstract class AbstractValidatedRequestTestCase extends TestCase
{
    use AbstractValidatedRequestTrait;
}
