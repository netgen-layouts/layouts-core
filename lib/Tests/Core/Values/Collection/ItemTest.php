<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;

class ItemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValueId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValueType
     */
    public function testSetDefaultProperties()
    {
        $item = new Item();

        self::assertNull($item->getId());
        self::assertNull($item->getStatus());
        self::assertNull($item->getCollectionId());
        self::assertNull($item->getPosition());
        self::assertNull($item->getType());
        self::assertNull($item->getValueId());
        self::assertNull($item->getValueType());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValueId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValueType
     */
    public function testSetProperties()
    {
        $item = new Item(
            array(
                'id' => 42,
                'status' => Collection::STATUS_PUBLISHED,
                'collectionId' => 30,
                'position' => 3,
                'type' => Item::TYPE_OVERRIDE,
                'valueId' => 32,
                'valueType' => 'ezcontent',
            )
        );

        self::assertEquals(42, $item->getId());
        self::assertEquals(Collection::STATUS_PUBLISHED, $item->getStatus());
        self::assertEquals(30, $item->getCollectionId());
        self::assertEquals(3, $item->getPosition());
        self::assertEquals(Item::TYPE_OVERRIDE, $item->getType());
        self::assertEquals(32, $item->getValueId());
        self::assertEquals('ezcontent', $item->getValueType());
    }
}
