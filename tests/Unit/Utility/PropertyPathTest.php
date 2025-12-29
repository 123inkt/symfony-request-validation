<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Utility;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use DigitalRevolution\SymfonyRequestValidation\Utility\PropertyPath;
use PHPUnit\Framework\TestCase;

#[CoversClass(PropertyPath::class)]
class PropertyPathTest extends TestCase
{
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
