<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Item;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
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
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isPublished
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
        $this->assertNull($item->isPublished());
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
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isPublished
     */
    public function testSetProperties()
    {
        $item = new Item(
            array(
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 30,
                'position' => 3,
                'type' => Item::TYPE_OVERRIDE,
                'valueId' => 32,
                'valueType' => 'ezcontent',
                'published' => true,
            )
        );

        $this->assertEquals(42, $item->getId());
        $this->assertTrue($item->isPublished());
        $this->assertEquals(30, $item->getCollectionId());
        $this->assertEquals(3, $item->getPosition());
        $this->assertEquals(Item::TYPE_OVERRIDE, $item->getType());
        $this->assertEquals(32, $item->getValueId());
        $this->assertEquals('ezcontent', $item->getValueType());
        $this->assertTrue($item->isPublished());
    }
}
