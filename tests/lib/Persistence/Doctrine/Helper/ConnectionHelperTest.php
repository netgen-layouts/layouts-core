<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConnectionHelper::class)]
final class ConnectionHelperTest extends TestCase
{
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
            ->willReturn(42);

        self::assertSame(42, $helper->lastId('table'));
    }
}
