<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Traversable;

class ResultSetTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getCollection
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getResults
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getTotalCount
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getOffset
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getLimit
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getIterator
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetExists
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetGet
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetSet
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetUnset
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::count
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

        $this->assertInstanceOf(Traversable::class, $result->getIterator());
        $this->assertEquals(array('items'), iterator_to_array($result->getIterator()));

        $this->assertCount(1, $result);

        $this->assertTrue(isset($result[0]));
        $this->assertEquals('items', $result[0]);

        try {
            $result[0] = 'new';
            $this->fail('Succeeded in setting a new value to result set.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($result[0]);
            $this->fail('Succeeded in unsetting a value in result set.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }
}
