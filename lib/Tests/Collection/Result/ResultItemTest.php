<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ResultItem;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use PHPUnit\Framework\TestCase;

class ResultItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultItem::getItem
     * @covers \Netgen\BlockManager\Collection\Result\ResultItem::getCollectionItem
     * @covers \Netgen\BlockManager\Collection\Result\ResultItem::getType
     * @covers \Netgen\BlockManager\Collection\Result\ResultItem::getPosition
     */
    public function testObject()
    {
        $resultItem = new ResultItem(
            array(
                'item' => new Item(),
                'collectionItem' => new CollectionItem(),
                'type' => ResultItem::TYPE_MANUAL,
                'position' => 3,
            )
        );

        $this->assertEquals(new Item(), $resultItem->getItem());
        $this->assertEquals(new CollectionItem(), $resultItem->getCollectionItem());
        $this->assertEquals(ResultItem::TYPE_MANUAL, $resultItem->getType());
        $this->assertEquals(3, $resultItem->getPosition());
    }
}
