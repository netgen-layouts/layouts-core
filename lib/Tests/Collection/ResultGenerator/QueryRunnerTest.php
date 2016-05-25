<?php

namespace Netgen\BlockManager\Tests\Collection\ResultGenerator;

use Netgen\BlockManager\Collection\ResultGenerator\QueryRunner;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryRunner\QueryType;

class QueryRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface
     */
    protected $queryRunner;

    public function setUp()
    {
        $this->queryTypeRegistry = new QueryTypeRegistry();

        $this->queryRunner = new QueryRunner($this->queryTypeRegistry);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param array $expectedResult
     *
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runQueries
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::getTotalCount
     * @dataProvider runSingleQueryProvider
     */
    public function testRunSingleQuery($offset, $limit, $expectedResult)
    {
        $this->queryTypeRegistry->addQueryType(
            new QueryType('query', array(40, 41, 42, 43, 44, 45, 46, 47, 48))
        );

        $query = array(new Query(array('type' => 'query')));

        $results = $this->queryRunner->runQueries($query, $offset, $limit);
        $resultCount = $this->queryRunner->getTotalCount($query);

        self::assertEquals($expectedResult, $results);
        self::assertEquals(9, $resultCount);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param array $expectedResult
     *
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runQueries
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::getTotalCount
     * @dataProvider runMultipleQueriesProvider
     */
    public function testRunMultipleQueries($offset, $limit, $expectedResult)
    {
        $this->queryTypeRegistry->addQueryType(
            new QueryType('query1', array(40, 41, 42, 43, 44, 45, 46, 47, 48))
        );

        $this->queryTypeRegistry->addQueryType(
            new QueryType('query2', array(52, 53, 54, 55, 56))
        );

        $this->queryTypeRegistry->addQueryType(
            new QueryType('query3', array(62, 63, 64, 65, 66, 67, 68, 69))
        );

        $this->queryTypeRegistry->addQueryType(
            new QueryType('query4', array(70, 71, 72, 73, 74, 75, 76))
        );

        $queries = array(
            new Query(array('type' => 'query1')),
            new Query(array('type' => 'query2')),
            new Query(array('type' => 'query3')),
            new Query(array('type' => 'query4')),
        );

        $results = $this->queryRunner->runQueries($queries, $offset, $limit);
        $resultCount = $this->queryRunner->getTotalCount($queries);

        self::assertEquals($expectedResult, $results);
        self::assertEquals(29, $resultCount);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runQueries
     * @expectedException \RuntimeException
     */
    public function testRunQueriesThrowsRuntimeException()
    {
        $this->queryRunner->runQueries(array());
    }

    public function runSingleQueryProvider()
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

    public function runMultipleQueriesProvider()
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
