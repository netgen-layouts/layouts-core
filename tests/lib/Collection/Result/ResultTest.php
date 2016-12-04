<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\Item;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\Result::getItem
     * @covers \Netgen\BlockManager\Collection\Result\Result::getCollectionItem
     * @covers \Netgen\BlockManager\Collection\Result\Result::getType
     * @covers \Netgen\BlockManager\Collection\Result\Result::getPosition
     */
    public function testObject()
    {
        $resultItem = new Result(
            array(
                'item' => new Item(),
                'collectionItem' => new CollectionItem(),
                'type' => Result::TYPE_MANUAL,
                'position' => 3,
            )
        );

        $this->assertEquals(new Item(), $resultItem->getItem());
        $this->assertEquals(new CollectionItem(), $resultItem->getCollectionItem());
        $this->assertEquals(Result::TYPE_MANUAL, $resultItem->getType());
        $this->assertEquals(3, $resultItem->getPosition());
    }
}
