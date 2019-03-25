<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Utils;

use Netgen\BlockManager\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class HydratorTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Utils\HydratorTrait::fromArray
     * @covers \Netgen\BlockManager\Utils\HydratorTrait::initHydrator
     */
    public function testFromArray(): void
    {
        $value = Value::fromArray(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('foo', $value->getA());
        self::assertSame('bar', $value->getB());
        self::assertSame('baz', $value->getC());
    }

    /**
     * @covers \Netgen\BlockManager\Utils\HydratorTrait::hydrate
     * @covers \Netgen\BlockManager\Utils\HydratorTrait::initHydrator
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
