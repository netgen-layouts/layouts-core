<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getName
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getQueries
     */
    public function testSetDefaultProperties()
    {
        $collection = new Collection();

        $this->assertNull($collection->getId());
        $this->assertNull($collection->getStatus());
        $this->assertNull($collection->getType());
        $this->assertNull($collection->getName());
        $this->assertEquals(array(), $collection->getItems());
        $this->assertEquals(array(), $collection->getManualItems());
        $this->assertEquals(array(), $collection->getOverrideItems());
        $this->assertEquals(array(), $collection->getQueries());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getName
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getQueries
     */
    public function testSetProperties()
    {
        $collection = new Collection(
            array(
                'id' => 42,
                'status' => Collection::STATUS_PUBLISHED,
                'type' => Collection::TYPE_NAMED,
                'name' => 'My collection',
                'items' => array(
                    new Item(array('type' => Item::TYPE_MANUAL, 'position' => 3)),
                    new Item(array('type' => Item::TYPE_OVERRIDE, 'position' => 5)),
                ),
                'queries' => array(
                    new Query(array('identifier' => 'my_query')),
                ),
            )
        );

        $this->assertEquals(42, $collection->getId());
        $this->assertEquals(Collection::STATUS_PUBLISHED, $collection->getStatus());
        $this->assertEquals(Collection::TYPE_NAMED, $collection->getType());
        $this->assertEquals('My collection', $collection->getName());
        $this->assertCount(2, $collection->getItems());
        $this->assertCount(1, $collection->getManualItems());
        $this->assertCount(1, $collection->getOverrideItems());
        $this->assertCount(1, $collection->getQueries());

        $this->assertEquals(Item::TYPE_MANUAL, $collection->getItems()[0]->getType());
        $this->assertEquals(Item::TYPE_OVERRIDE, $collection->getItems()[1]->getType());
        $this->assertEquals(Item::TYPE_MANUAL, $collection->getManualItems()[3]->getType());
        $this->assertEquals(Item::TYPE_OVERRIDE, $collection->getOverrideItems()[5]->getType());

        $this->assertEquals('my_query', $collection->getQueries()[0]->getIdentifier());
    }
}
