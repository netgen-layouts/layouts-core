<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Core\Values\LazyCollection;
use PHPUnit\Framework\TestCase;

final class LazyCollectionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Values\LazyCollection
     */
    private $collection;

    public function setUp(): void
    {
        $this->collection = new LazyCollection(function (): array { return [1, 2, 3]; });
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LazyCollection::__construct
     * @covers \Netgen\BlockManager\Core\Values\LazyCollection::doInitialize
     */
    public function testToArray(): void
    {
        self::assertFalse($this->collection->isInitialized());

        self::assertSame([1, 2, 3], $this->collection->toArray());

        self::assertTrue($this->collection->isInitialized());
    }
}
