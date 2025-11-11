<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler;
use Netgen\Layouts\Persistence\Values\Status;
use Netgen\Layouts\Tests\Persistence\Doctrine\Stubs\EmptyQueryHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryHandler::class)]
final class QueryHandlerTest extends TestCase
{
    private Connection $databaseConnection;

    private EmptyQueryHandler $queryHandler;

    protected function setUp(): void
    {
        $this->databaseConnection = DriverManager::getConnection(
            [
                'url' => 'sqlite://:memory:',
            ],
        );

        $this->queryHandler = new EmptyQueryHandler();
    }

    public function testApplyIdCondition(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyIdCondition($query, 1);

        self::assertSame(['id' => 1], $query->getParameters());
    }

    public function testApplyIdConditionWithUuid(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyIdCondition($query, '205d4dbb-b9e9-45d5-9dac-642730d29457');

        self::assertSame(['uuid' => '205d4dbb-b9e9-45d5-9dac-642730d29457'], $query->getParameters());
    }

    public function testApplyStatusCondition(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyStatusCondition($query, Status::Published);

        self::assertSame(['status' => Status::Published->value], $query->getParameters());
    }

    public function testApplyOffsetAndLimit(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $this->queryHandler->applyOffsetAndLimit($query, 10, 15);

        self::assertSame(10, $query->getFirstResult());
        self::assertSame(15, $query->getMaxResults());
    }
}
