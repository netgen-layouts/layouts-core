<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\Collection\Result\QueryIterator;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryIteratorTest extends TestCase
{
    use IteratorTestTrait;

    /**
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::buildIterator
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::count
     */
    public function testWithQuery()
    {
        $queryItems = array(40, 41, 42, 43, 44, 45, 46, 47, 48);

        $queryType = new QueryType('query', $queryItems);
        $query = new Query(array('queryType' => $queryType));

        $queryIterator = new QueryIterator($query);

        $this->assertIteratorValues($queryItems, $queryIterator);
        $this->assertEquals(9, $queryIterator->count());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::__construct
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::buildIterator
     * @covers \Netgen\BlockManager\Collection\Result\QueryIterator::count
     */
    public function testWithNoQuery()
    {
        $queryIterator = new ArrayIterator();

        $this->assertIteratorValues(array(), $queryIterator);
        $this->assertEquals(0, $queryIterator->count());
    }
}
