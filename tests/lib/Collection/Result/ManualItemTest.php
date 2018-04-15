<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Item\Item as CmsItem;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ManualItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getCollectionItem
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getName
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getObject
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getRemoteId
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getValue
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getValueType
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::isVisible
     */
    public function testObject()
    {
        $collectionItem = new Item(
            array(
                'cmsItem' => new CmsItem(
                    array(
                        'value' => 42,
                        'remoteId' => 'abc',
                        'valueType' => 'type',
                        'name' => 'Value name',
                        'isVisible' => true,
                        'object' => new stdClass(),
                    )
                ),
            )
        );

        $value = new ManualItem($collectionItem);

        $this->assertEquals($collectionItem, $value->getCollectionItem());
        $this->assertEquals(42, $value->getValue());
        $this->assertEquals('abc', $value->getRemoteId());
        $this->assertEquals('type', $value->getValueType());
        $this->assertEquals('Value name', $value->getName());
        $this->assertTrue($value->isVisible());
        $this->assertEquals(new stdClass(), $value->getObject());
    }
}
