<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Core\Values\LazyCollection;
use PHPUnit\Framework\TestCase;

final class LazyCollectionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Values\LazyCollection
     */
    private $collection;

    public function setUp()
    {
        $this->collection = new LazyCollection(function () { return [1, 2, 3]; });
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LazyCollection::__construct
     * @covers \Netgen\BlockManager\Core\Values\LazyCollection::doInitialize
     */
    public function testToArray()
    {
        $this->assertFalse($this->collection->isInitialized());

        $this->assertEquals([1, 2, 3], $this->collection->toArray());

        $this->assertTrue($this->collection->isInitialized());
    }
}
