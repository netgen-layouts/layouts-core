<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use PHPUnit\Framework\TestCase;

use function class_exists;

// Deprecated DBAL 2 compatibility
class_exists('Doctrine\DBAL\Platforms\MySqlPlatform');
class_exists('Doctrine\DBAL\Platforms\PostgreSqlPlatform');

final class ConnectionHelperTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::getHandler
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::nextId
     */
    public function testNextId(): void
    {
        $platform = new MySQLPlatform();
        $connectionMock = $this->createMock(Connection::class);
        $helper = new ConnectionHelper($connectionMock);

        $connectionMock
            ->method('getDatabasePlatform')
            ->willReturn($platform);

        self::assertSame('null', $helper->nextId('table'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::getHandler
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::nextId
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::nextId
     */
    public function testNextIdForPostgres(): void
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::getHandler
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::lastId
     */
    public function testLastId(): void
    {
        $platform = new MySQLPlatform();
        $connectionMock = $this->createMock(Connection::class);
        $helper = new ConnectionHelper($connectionMock);

        $connectionMock
            ->method('getDatabasePlatform')
            ->willReturn($platform);

        $connectionMock
            ->method('lastInsertId')
            ->with(self::identicalTo('table'))
            ->willReturn(42);

        self::assertSame(42, $helper->lastId('table'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::getHandler
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::lastId
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::lastId
     */
    public function testLastIdForPostgres(): void
    {
        $platform = new PostgreSQLPlatform();
        $connectionMock = $this->createMock(Connection::class);
        $helper = new ConnectionHelper($connectionMock);

        $connectionMock
            ->method('getDatabasePlatform')
            ->willReturn($platform);

        $connectionMock
            ->method('lastInsertId')
            ->with(self::identicalTo('table_id_seq'))
            ->willReturn('43');

        self::assertSame('43', $helper->lastId('table'));
    }
}
