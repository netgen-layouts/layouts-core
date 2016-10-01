<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Collection\Result\CollectionQueryIterator;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

class CollectionQueryIteratorTest extends TestCase
{
    use IteratorTestTrait;

    /**
     * @param int $offset
     * @param int $limit
     * @param array $expectedResult
     *
     * @dataProvider singleQueryProvider
     */
    public function testWithSingleQuery($offset, $limit, $expectedResult)
    {
        $queryType = new QueryType('query', array(40, 41, 42, 43, 44, 45, 46, 47, 48));
        $query = new Query(array('queryType' => $queryType));

        $queryIterator = new CollectionQueryIterator(
            new Collection(array('queries' => array($query))),
            $offset,
            $limit
        );

        $this->assertIteratorValues($expectedResult, $queryIterator->getIterator());
        $this->assertEquals(9, $queryIterator->count());
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param array $expectedResult
     *
     * @dataProvider multipleQueriesProvider
     */
    public function testWithMultipleQueries($offset, $limit, $expectedResult)
    {
        $queryType1 = new QueryType('query1', array(40, 41, 42, 43, 44, 45, 46, 47, 48));
        $queryType2 = new QueryType('query2', array(52, 53, 54, 55, 56));
        $queryType3 = new QueryType('query3', array(62, 63, 64, 65, 66, 67, 68, 69));
        $queryType4 = new QueryType('query4', array(70, 71, 72, 73, 74, 75, 76));

        $queries = array(
            new Query(array('queryType' => $queryType1)),
            new Query(array('queryType' => $queryType2)),
            new Query(array('queryType' => $queryType3)),
            new Query(array('queryType' => $queryType4)),
        );

        $queryIterator = new CollectionQueryIterator(
            new Collection(array('queries' => $queries)),
            $offset,
            $limit
        );

        $this->assertIteratorValues($expectedResult, $queryIterator->getIterator());
        $this->assertEquals(29, $queryIterator->count());
    }

    public function testWithEmptyQueries()
    {
        $queryIterator = new CollectionQueryIterator(new Collection());

        $this->assertIteratorValues(array(), $queryIterator->getIterator());
        $this->assertEquals(0, $queryIterator->count());
    }

    public function singleQueryProvider()
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

    public function multipleQueriesProvider()
    {
        return array(
            array(0, 0, array()),
            array(0, 6, array(40, 41, 42, 43, 44, 45)),
            array(0, 9, array(40, 41, 42, 43, 44, 45, 46, 47, 48)),
            array(0, 12, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54)),
            array(0, 14, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56)),
            array(0, 18, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65)),
            array(0, 22, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69)),
            array(0, 25, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72)),
            array(0, 29, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(0, 33, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),

            array(3, 0, array()),
            array(3, 3, array(43, 44, 45)),
            array(3, 6, array(43, 44, 45, 46, 47, 48)),
            array(3, 9, array(43, 44, 45, 46, 47, 48, 52, 53, 54)),
            array(3, 11, array(43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56)),
            array(3, 15, array(43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65)),
            array(3, 19, array(43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69)),
            array(3, 22, array(43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72)),
            array(3, 26, array(43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(3, 30, array(43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),

            array(9, 0, array()),
            array(9, 3, array(52, 53, 54)),
            array(9, 5, array(52, 53, 54, 55, 56)),
            array(9, 9, array(52, 53, 54, 55, 56, 62, 63, 64, 65)),
            array(9, 13, array(52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69)),
            array(9, 16, array(52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72)),
            array(9, 20, array(52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(9, 24, array(52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),

            array(12, 0, array()),
            array(12, 2, array(55, 56)),
            array(12, 6, array(55, 56, 62, 63, 64, 65)),
            array(12, 10, array(55, 56, 62, 63, 64, 65, 66, 67, 68, 69)),
            array(12, 13, array(55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72)),
            array(12, 17, array(55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(12, 21, array(55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),

            array(14, 0, array()),
            array(14, 4, array(62, 63, 64, 65)),
            array(14, 8, array(62, 63, 64, 65, 66, 67, 68, 69)),
            array(14, 11, array(62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72)),
            array(14, 15, array(62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(14, 19, array(62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),

            array(18, 0, array()),
            array(18, 4, array(66, 67, 68, 69)),
            array(18, 7, array(66, 67, 68, 69, 70, 71, 72)),
            array(18, 11, array(66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(18, 15, array(66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),

            array(22, 0, array()),
            array(22, 3, array(70, 71, 72)),
            array(22, 7, array(70, 71, 72, 73, 74, 75, 76)),
            array(22, 11, array(70, 71, 72, 73, 74, 75, 76)),

            array(25, 0, array()),
            array(25, 4, array(73, 74, 75, 76)),
            array(25, 8, array(73, 74, 75, 76)),

            array(29, 0, array()),
            array(29, 4, array()),

            array(33, 0, array()),
            array(33, 4, array()),

            array(0, null, array(40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(6, null, array(46, 47, 48, 52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(9, null, array(52, 53, 54, 55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(12, null, array(55, 56, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(14, null, array(62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(17, null, array(65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76)),
            array(22, null, array(70, 71, 72, 73, 74, 75, 76)),
            array(25, null, array(73, 74, 75, 76)),
            array(29, null, array()),
            array(35, null, array()),
        );
    }
}
