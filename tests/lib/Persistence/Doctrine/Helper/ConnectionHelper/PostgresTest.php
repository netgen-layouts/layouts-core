<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class PostgresTest extends TestCase
{
    use DatabaseTrait;

    private Postgres $helper;

    protected function setUp(): void
    {
        $this->createDatabase();

        $this->helper = new Postgres($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    protected function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::nextId
     */
    public function testNextId(): void
    {
        $platform = new PostgreSQLPlatform();
        $connectionMock = $this->createMock(Connection::class);
        $helper = new ConnectionHelper($connectionMock);

        $connectionMock
            ->method('getDatabasePlatform')
            ->willReturn($platform);

        self::assertSame("nextval('table_id_seq')", $helper->nextId('table'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::lastId
     */
    public function testLastId(): void
    {
        if (!$this->databaseConnection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Test only runs on PostgresSQL.');
        }

        self::assertSame(38, $this->helper->lastId('nglayouts_block'));
    }
}
