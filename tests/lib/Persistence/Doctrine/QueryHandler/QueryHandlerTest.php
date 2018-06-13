<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\DriverManager;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\QueryHandler;
use PHPUnit\Framework\TestCase;

final class QueryHandlerTest extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $databaseConnection;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\QueryHandler
     */
    private $queryHandler;

    public function setUp()
    {
        $this->databaseConnection = DriverManager::getConnection(
            [
                'url' => 'sqlite://:memory:',
            ]
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

        $this->assertEquals(['status' => 1], $query->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\QueryHandler::applyOffsetAndLimit
     */
    public function testApplyOffsetAndLimit()
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyOffsetAndLimit($query, 10, 15);

        $this->assertEquals(10, $query->getFirstResult());
        $this->assertEquals(15, $query->getMaxResults());
    }
}
