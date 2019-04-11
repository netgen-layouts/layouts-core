<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Stubs\Value;
use Netgen\Layouts\Utils\Hydrator;
use PHPUnit\Framework\TestCase;

final class HydratorTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Utils\Hydrator
     */
    private $hydrator;

    public function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }

    /**
     * @covers \Netgen\Layouts\Utils\Hydrator::extract
     */
    public function testExtract(): void
    {
        self::assertSame(
            ['a' => 'foo', 'b' => 'bar', 'c' => 'baz'],
            $this->hydrator->extract(new Value('foo', 'bar', 'baz'))
        );
    }

    /**
     * @covers \Netgen\Layouts\Utils\Hydrator::hydrate
     */
    public function testHydrate(): void
    {
        $value = new Value();

        $this->hydrator->hydrate(['a' => 'foo', 'b' => 'bar', 'c' => 'baz'], $value);

        self::assertSame('foo', $value->getA());
        self::assertSame('bar', $value->getB());
        self::assertSame('baz', $value->getC());
    }

    /**
     * @covers \Netgen\Layouts\Utils\Hydrator::hydrate
     */
    public function testHydrateThrowsRuntimeExceptionWithNonExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "unknown" does not exist in "Netgen\Layouts\Tests\Stubs\Value" class.');

        $this->hydrator->hydrate(['unknown' => 'foo'], new Value());
    }
}
