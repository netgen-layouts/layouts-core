<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
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

        $this->assertNull($item->getId());
        $this->assertNull($item->getStatus());
        $this->assertNull($item->getCollectionId());
        $this->assertNull($item->getPosition());
        $this->assertNull($item->getType());
        $this->assertNull($item->getValueId());
        $this->assertNull($item->getValueType());
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

        $this->assertEquals(42, $item->getId());
        $this->assertEquals(Collection::STATUS_PUBLISHED, $item->getStatus());
        $this->assertEquals(30, $item->getCollectionId());
        $this->assertEquals(3, $item->getPosition());
        $this->assertEquals(Item::TYPE_OVERRIDE, $item->getType());
        $this->assertEquals(32, $item->getValueId());
        $this->assertEquals('ezcontent', $item->getValueType());
    }
}
