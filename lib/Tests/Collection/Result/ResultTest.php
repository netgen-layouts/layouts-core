<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\Result::getCollection
     * @covers \Netgen\BlockManager\Collection\Result\Result::getResults
     * @covers \Netgen\BlockManager\Collection\Result\Result::getTotalCount
     * @covers \Netgen\BlockManager\Collection\Result\Result::getOffset
     * @covers \Netgen\BlockManager\Collection\Result\Result::getLimit
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

        $this->assertEquals(new Collection(), $result->getCollection());
        $this->assertEquals(array('items'), $result->getResults());
        $this->assertEquals(15, $result->getTotalCount());
        $this->assertEquals(3, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());
    }
}
