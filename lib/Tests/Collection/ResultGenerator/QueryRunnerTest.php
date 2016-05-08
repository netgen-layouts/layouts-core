<?php

namespace Netgen\BlockManager\Tests\Collection\ResultGenerator;

use Netgen\BlockManager\Collection\ResultGenerator\QueryRunner;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Core\Values\Collection\Query;

class QueryRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeMock;

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
        $this->queryTypeMock = $this->getMock(QueryTypeInterface::class);

        $this->queryTypeMock
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('query'));

        $this->queryTypeRegistry = new QueryTypeRegistry();
        $this->queryTypeRegistry->addQueryType($this->queryTypeMock);

        $this->queryRunner = new QueryRunner($this->queryTypeRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runQueries
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runSingleQuery
     */
    public function testRunSingleQuery()
    {
        $query = new Query(
            array(
                'type' => 'query',
                'parameters' => array(
                    'param' => 'value',
                ),
            )
        );

        $this->queryTypeMock
            ->expects($this->once())
            ->method('getValues')
            ->with($this->equalTo($query->getParameters(), 20, 10))
            ->will($this->returnValue(array(42, 43, 44)));

        $queryResult = $this->queryRunner->runQueries(array($query), 20, 10);

        self::assertEquals(array(42, 43, 44), $queryResult);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runQueries
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runMultipleQueries
     */
    public function testRunMultipleQueries()
    {
        $query1 = new Query(
            array(
                'type' => 'query',
                'parameters' => array(
                    'param1' => 'value1',
                ),
            )
        );

        $query2 = new Query(
            array(
                'type' => 'query',
                'parameters' => array(
                    'param2' => 'value2',
                ),
            )
        );

        $query3 = new Query(
            array(
                'type' => 'query',
                'parameters' => array(
                    'param3' => 'value3',
                ),
            )
        );

        $this->queryRunner->runQueries(array($query1, $query2, $query3), 20, 10);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator\QueryRunner::runQueries
     * @expectedException \RuntimeException
     */
    public function testRunQueriesThrowsRuntimeException()
    {
        $this->queryRunner->runQueries(array());
    }
}
