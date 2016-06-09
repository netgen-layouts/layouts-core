<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\Result;
use Netgen\BlockManager\Core\Values\Collection\Collection;

class ResultTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result::getCollection
     * @covers \Netgen\BlockManager\Collection\Result::getResults
     * @covers \Netgen\BlockManager\Collection\Result::getTotalCount
     * @covers \Netgen\BlockManager\Collection\Result::getOffset
     * @covers \Netgen\BlockManager\Collection\Result::getLimit
     */
    public function testObject()
    {
        $result = new Result(
            array(
                'collection' => new Collection(),
                'results' => array('items'),
                'totalCount' => 15,
                'offset' => 3,
                'limit' => 5,
            )
        );

        self::assertEquals(new Collection(), $result->getCollection());
        self::assertEquals(array('items'), $result->getResults());
        self::assertEquals(15, $result->getTotalCount());
        self::assertEquals(3, $result->getOffset());
        self::assertEquals(5, $result->getLimit());
    }
}
