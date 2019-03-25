<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Utils;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Tests\Stubs\Value;
use Netgen\BlockManager\Utils\Hydrator;
use PHPUnit\Framework\TestCase;

final class HydratorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Utils\Hydrator
     */
    private $hydrator;

    public function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }

    /**
     * @covers \Netgen\BlockManager\Utils\Hydrator::extract
     */
    public function testExtract(): void
    {
        self::assertSame(
            ['a' => 'foo', 'b' => 'bar', 'c' => 'baz'],
            $this->hydrator->extract(new Value('foo', 'bar', 'baz'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Utils\Hydrator::extract
     */
    public function testExtractThrowsRuntimeExceptionWithNonObject(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('extract expects the provided $object to be a PHP object');

        $this->hydrator->extract('foo');
    }

    /**
     * @covers \Netgen\BlockManager\Utils\Hydrator::hydrate
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
     * @covers \Netgen\BlockManager\Utils\Hydrator::hydrate
     */
    public function testHydrateThrowsRuntimeExceptionWithNonObject(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('hydrate expects the provided $object to be a PHP object');

        $this->hydrator->hydrate(['a' => 'foo', 'b' => 'bar', 'c' => 'baz'], 'foo');
    }

    /**
     * @covers \Netgen\BlockManager\Utils\Hydrator::hydrate
     */
    public function testHydrateThrowsRuntimeExceptionWithNonExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "unknown" does not exist in "Netgen\BlockManager\Tests\Stubs\Value" class.');

        $this->hydrator->hydrate(['unknown' => 'foo'], new Value());
    }
}
