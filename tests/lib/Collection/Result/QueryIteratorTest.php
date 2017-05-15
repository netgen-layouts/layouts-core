<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\QueryIterator;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class QueryIteratorTest extends TestCase
{
    use IteratorTestTrait;

    /**
     * @param int $offset
     * @param int $limit
     * @param array $expectedResult
     *
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::buildIterator
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::count
     *
     * @dataProvider queryProvider
     */
    public function testWithQuery($offset, $limit, $expectedResult)
    {
        $queryType = new QueryType('query', array(40, 41, 42, 43, 44, 45, 46, 47, 48));
        $query = new Query(array('queryType' => $queryType));

        $queryIterator = new QueryIterator(
            new Collection(array('query' => $query)),
            $offset,
            $limit
        );

        $this->assertIteratorValues($expectedResult, $queryIterator);
        $this->assertEquals(9, $queryIterator->count());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::buildIterator
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::count
     */
    public function testWithNoQuery()
    {
        $queryIterator = new QueryIterator(new Collection());

        $this->assertIteratorValues(array(), $queryIterator);
        $this->assertEquals(0, $queryIterator->count());
    }

    public function queryProvider()
    {
        return array(
            array(0, 0, array()),
            array(0, 6, array(40, 41, 42, 43, 44, 45)),
            array(0, 9, array(40, 41, 42, 43, 44, 45, 46, 47, 48)),
            array(0, 12, array(40, 41, 42, 43, 44, 45, 46, 47, 48)),

            array(3, 0, array()),
            array(3, 3, array(43, 44, 45)),
            array(3, 6, array(43, 44, 45, 46, 47, 48)),
            array(3, 9, array(43, 44, 45, 46, 47, 48)),

            array(9, 0, array()),
            array(9, 3, array()),

            array(0, null, array(40, 41, 42, 43, 44, 45, 46, 47, 48)),
            array(6, null, array(46, 47, 48)),
            array(9, null, array()),
            array(12, null, array()),
        );
    }
}
