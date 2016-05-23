<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Value\Value;
use Netgen\BlockManager\Core\Values\Collection\Item;

class ResultItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\ResultItem::getValue
     * @covers \Netgen\BlockManager\Collection\ResultItem::getCollectionItem
     * @covers \Netgen\BlockManager\Collection\ResultItem::getType
     * @covers \Netgen\BlockManager\Collection\ResultItem::getPosition
     */
    public function testObject()
    {
        $resultItem = new ResultItem(
            array(
                'value' => new Value(),
                'collectionItem' => new Item(),
                'type' => ResultItem::TYPE_MANUAL,
                'position' => 3,
            )
        );

        self::assertEquals(new Value(), $resultItem->getValue());
        self::assertEquals(new Item(), $resultItem->getCollectionItem());
        self::assertEquals(ResultItem::TYPE_MANUAL, $resultItem->getType());
        self::assertEquals(3, $resultItem->getPosition());
    }
}
