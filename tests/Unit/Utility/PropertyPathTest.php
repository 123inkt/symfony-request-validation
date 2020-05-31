<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Utility;

use DigitalRevolution\SymfonyRequestValidation\Utility\PropertyPath;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Utility\PropertyPath
 */
class PropertyPathTest extends TestCase
{
    /**
     * @covers ::toArray
     */
    public function testToArray(): void
    {
        static::assertSame([], PropertyPath::toArray(null));
        static::assertSame([], PropertyPath::toArray(''));
        static::assertSame(['peter'], PropertyPath::toArray('peter'));
        static::assertSame(['peter', 'parker'], PropertyPath::toArray('peter.parker'));
        static::assertSame(['peter', 'parker'], PropertyPath::toArray('peter[parker]'));
        static::assertSame(['peter', 'parker'], PropertyPath::toArray('[peter][parker]'));
        static::assertSame(['peter', 'parker', 'name'], PropertyPath::toArray('[peter][parker].name'));
    }
}
