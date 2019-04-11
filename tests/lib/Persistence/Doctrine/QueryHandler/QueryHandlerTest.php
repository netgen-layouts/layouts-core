<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\DriverManager;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler;
use PHPUnit\Framework\TestCase;

final class QueryHandlerTest extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $databaseConnection;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler
     */
    private $queryHandler;

    public function setUp(): void
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler::applyStatusCondition
     */
    public function testApplyStatusCondition(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyStatusCondition($query, 1);

        self::assertSame(['status' => 1], $query->getParameters());
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler::applyOffsetAndLimit
     */
    public function testApplyOffsetAndLimit(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyOffsetAndLimit($query, 10, 15);

        self::assertSame(10, $query->getFirstResult());
        self::assertSame(15, $query->getMaxResults());
    }
}
