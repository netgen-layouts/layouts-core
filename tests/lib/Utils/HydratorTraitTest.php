<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class HydratorTraitTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Utils\HydratorTrait::fromArray
     * @covers \Netgen\Layouts\Utils\HydratorTrait::initHydrator
     */
    public function testFromArray(): void
    {
        $value = Value::fromArray(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo', $value->getA());
        self::assertSame('bar', $value->getB());
        self::assertSame('baz', $value->getC());
    }

    /**
     * @covers \Netgen\Layouts\Utils\HydratorTrait::hydrate
     * @covers \Netgen\Layouts\Utils\HydratorTrait::initHydrator
     */
    public function testHydrate(): void
    {
        $value = new Value();

        $value->hydrate(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo', $value->getA());
        self::assertSame('bar', $value->getB());
        self::assertSame('baz', $value->getC());
    }
}
