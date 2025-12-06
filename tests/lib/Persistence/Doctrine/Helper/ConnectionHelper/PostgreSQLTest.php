<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\PostgreSQL;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PostgreSQL::class)]
final class PostgreSQLTest extends TestCase
{
    use DatabaseTrait;

    private PostgreSQL $helper;

    protected function setUp(): void
    {
        $this->createDatabase();

        $this->helper = new PostgreSQL($this->databaseConnection);
    }

    public function testNextId(): void
    {
        $platform = new PostgreSQLPlatform();
        $connectionStub = self::createStub(Connection::class);
        $helper = new ConnectionHelper($connectionStub);

        $connectionStub
            ->method('getDatabasePlatform')
            ->willReturn($platform);

        self::assertSame("nextval('table_id_seq')", $helper->nextId('table'));
    }

    public function testLastId(): void
    {
        if (!$this->databaseConnection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Test only runs on PostgresSQL.');
        }

        self::assertSame(38, $this->helper->lastId('nglayouts_block'));
    }
}
