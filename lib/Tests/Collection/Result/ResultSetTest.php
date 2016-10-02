<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

class ResultSetTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getCollection
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getResults
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getTotalCount
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getOffset
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getLimit
     */
    public function testObject()
    {
        $result = new ResultSet(
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
