<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Tests\Stubs\Value;
use Netgen\Layouts\Utils\HydratorTrait;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

#[CoversTrait(HydratorTrait::class)]
final class HydratorTraitTest extends TestCase
{
    public function testFromArray(): void
    {
        $value = Value::fromArray(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo', $value->a);
        self::assertSame('bar', $value->b);
        self::assertSame('baz', $value->c);
    }

    public function testHydrate(): void
    {
        $value = new Value();

        $value->hydrate(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo', $value->a);
        self::assertSame('bar', $value->b);
        self::assertSame('baz', $value->c);
    }
}
