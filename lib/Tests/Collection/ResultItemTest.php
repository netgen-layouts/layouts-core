<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;

class ResultItemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\ResultItem::getItem
     * @covers \Netgen\BlockManager\Collection\ResultItem::getCollectionItem
     * @covers \Netgen\BlockManager\Collection\ResultItem::getType
     * @covers \Netgen\BlockManager\Collection\ResultItem::getPosition
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

        self::assertEquals(new Item(), $resultItem->getItem());
        self::assertEquals(new CollectionItem(), $resultItem->getCollectionItem());
        self::assertEquals(ResultItem::TYPE_MANUAL, $resultItem->getType());
        self::assertEquals(3, $resultItem->getPosition());
    }
}
