<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use PHPUnit\Framework\TestCase;

final class ConnectionHelperTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $databasePlatformMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $databaseConnectionMock;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper
     */
    private $connectionHelper;

    protected function setUp(): void
    {
        $this->databasePlatformMock = $this->createMock(AbstractPlatform::class);
        $this->databaseConnectionMock = $this->createMock(Connection::class);

        $this->databaseConnectionMock
            ->expects(self::any())
            ->method('getDatabasePlatform')
            ->willReturn($this->databasePlatformMock);

        $this->connectionHelper = new ConnectionHelper($this->databaseConnectionMock);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::nextId
     */
    public function testNextId(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->willReturn('mysql');

        self::assertSame('null', $this->connectionHelper->nextId('table'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::nextId
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::nextId
     */
    public function testNextIdForPostgres(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->willReturn('postgresql');

        $this->databasePlatformMock
            ->expects(self::once())
            ->method('getIdentitySequenceName')
            ->with(self::identicalTo('table'), self::identicalTo('id'))
            ->willReturn('s_table_id');

        self::assertSame("nextval('s_table_id')", $this->connectionHelper->nextId('table'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::lastId
     */
    public function testLastId(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->willReturn('mysql');

        $this->databaseConnectionMock
            ->expects(self::any())
            ->method('lastInsertId')
            ->with(self::identicalTo('table'))
            ->willReturn(42);

        self::assertSame(42, $this->connectionHelper->lastId('table'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper::lastId
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::lastId
     */
    public function testLastIdForPostgres(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->willReturn('postgresql');

        $this->databasePlatformMock
            ->expects(self::once())
            ->method('getIdentitySequenceName')
            ->with(self::identicalTo('table'), self::identicalTo('id'))
            ->willReturn('s_table_id');

        $this->databaseConnectionMock
            ->expects(self::any())
            ->method('lastInsertId')
            ->with(self::identicalTo('s_table_id'))
            ->willReturn(43);

        self::assertSame(43, $this->connectionHelper->lastId('table'));
    }
}
