<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\QueryHandler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\QueryHandler;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class QueryHandlerTest extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\QueryHandler
     */
    protected $queryHandler;

    public function setUp()
    {
        $this->databaseConnection = DriverManager::getConnection(
            array(
                'url' => 'sqlite://:memory:',
            )
        );

        $this->queryHandler = $this->getMockBuilder(QueryHandler::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\QueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\QueryHandler::applyStatusCondition
     */
    public function testApplyStatusCondition()
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyStatusCondition($query, 1);

        $this->assertEquals(array('status' => 1), $query->getParameters());
    }
}
