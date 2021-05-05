<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class QueryHandlerTest extends TestCase
{
    private Connection $databaseConnection;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler
     */
    private MockObject $queryHandler;

    protected function setUp(): void
    {
        $this->databaseConnection = DriverManager::getConnection(
            [
                'url' => 'sqlite://:memory:',
            ],
        );

        $this->queryHandler = $this->getMockBuilder(QueryHandler::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler::applyIdCondition
     */
    public function testApplyIdCondition(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyIdCondition($query, 1);

        self::assertSame(['id' => 1], $query->getParameters());
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler::applyIdCondition
     */
    public function testApplyIdConditionWithUuid(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyIdCondition($query, '205d4dbb-b9e9-45d5-9dac-642730d29457');

        self::assertSame(['uuid' => '205d4dbb-b9e9-45d5-9dac-642730d29457'], $query->getParameters());
    }

    /**
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
