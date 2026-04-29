<?php

declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Test;

use PHPUnit\Framework\TestCase;

/**
 * @codeCoverageIgnore For test purposes only
 */
abstract class AbstractValidatedRequestTestCase extends TestCase
{
    use AbstractValidatedRequestTrait;
}
