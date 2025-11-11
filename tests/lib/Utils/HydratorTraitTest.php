<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Tests\Stubs\Value;
use Netgen\Layouts\Utils\HydratorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HydratorTrait::class)]
final class HydratorTraitTest extends TestCase
{
    public function testFromArray(): void
    {
        $value = Value::fromArray(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo', $value->getA());
        self::assertSame('bar', $value->getB());
        self::assertSame('baz', $value->getC());
    }

    public function testHydrate(): void
    {
        $value = new Value();

        $value->hydrate(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo', $value->getA());
        self::assertSame('bar', $value->getB());
        self::assertSame('baz', $value->getC());
    }
}
