<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Stubs\Value;
use Netgen\Layouts\Utils\Hydrator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Hydrator::class)]
final class HydratorTest extends TestCase
{
    private Hydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }

    public function testExtract(): void
    {
        $value = new Value()->hydrate(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame(
            ['a' => 'foo', 'b' => 'bar', 'c' => 'baz'],
            $this->hydrator->extract($value),
        );
    }

    public function testHydrate(): void
    {
        $value = new Value();

        $this->hydrator->hydrate(['a' => 'foo', 'b' => 'bar', 'c' => 'baz'], $value);

        self::assertSame('foo', $value->getA());
        self::assertSame('bar', $value->getB());
        self::assertSame('baz', $value->getC());
    }

    public function testHydrateThrowsRuntimeExceptionWithNonExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "unknown" does not exist in "Netgen\Layouts\Tests\Stubs\Value" class.');

        $this->hydrator->hydrate(['unknown' => 'foo'], new Value());
    }
}
