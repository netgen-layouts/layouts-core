<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;

class CollectionTest extends \PHPUnit_Framework_TestCase
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

        self::assertNull($collection->getId());
        self::assertNull($collection->getStatus());
        self::assertNull($collection->getType());
        self::assertNull($collection->getName());
        self::assertEquals(array(), $collection->getItems());
        self::assertEquals(array(), $collection->getManualItems());
        self::assertEquals(array(), $collection->getOverrideItems());
        self::assertEquals(array(), $collection->getQueries());
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

        self::assertEquals(42, $collection->getId());
        self::assertEquals(Collection::STATUS_PUBLISHED, $collection->getStatus());
        self::assertEquals(Collection::TYPE_NAMED, $collection->getType());
        self::assertEquals('My collection', $collection->getName());
        self::assertCount(2, $collection->getItems());
        self::assertCount(1, $collection->getManualItems());
        self::assertCount(1, $collection->getOverrideItems());
        self::assertCount(1, $collection->getQueries());

        self::assertEquals(Item::TYPE_MANUAL, $collection->getItems()[0]->getType());
        self::assertEquals(Item::TYPE_OVERRIDE, $collection->getItems()[1]->getType());
        self::assertEquals(Item::TYPE_MANUAL, $collection->getManualItems()[3]->getType());
        self::assertEquals(Item::TYPE_OVERRIDE, $collection->getOverrideItems()[5]->getType());

        self::assertEquals('my_query', $collection->getQueries()[0]->getIdentifier());
    }
}
