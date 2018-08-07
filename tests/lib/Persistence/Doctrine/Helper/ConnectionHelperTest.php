<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
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
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    private $connectionHelper;

    public function setUp(): void
    {
        $this->databasePlatformMock = $this->createMock(AbstractPlatform::class);
        $this->databaseConnectionMock = $this->createMock(Connection::class);

        $this->databaseConnectionMock
            ->expects(self::any())
            ->method('getDatabasePlatform')
            ->will(self::returnValue($this->databasePlatformMock));

        $this->connectionHelper = new ConnectionHelper($this->databaseConnectionMock);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::getAutoIncrementValue
     */
    public function testGetAutoIncrementValue(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->will(self::returnValue('mysql'));

        self::assertSame('null', $this->connectionHelper->getAutoIncrementValue('table'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::getAutoIncrementValue
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::getAutoIncrementValue
     */
    public function testGetAutoIncrementValueForPostgres(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->will(self::returnValue('postgresql'));

        $this->databasePlatformMock
            ->expects(self::once())
            ->method('getIdentitySequenceName')
            ->with(self::identicalTo('table'), self::identicalTo('id'))
            ->will(self::returnValue('s_table_id'));

        self::assertSame("nextval('s_table_id')", $this->connectionHelper->getAutoIncrementValue('table'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::lastInsertId
     */
    public function testLastInsertId(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->will(self::returnValue('mysql'));

        $this->databaseConnectionMock
            ->expects(self::any())
            ->method('lastInsertId')
            ->with(self::identicalTo('table'))
            ->will(self::returnValue(42));

        self::assertSame(42, $this->connectionHelper->lastInsertId('table'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::lastInsertId
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::lastInsertId
     */
    public function testLastInsertIdForPostgres(): void
    {
        $this->databasePlatformMock
            ->expects(self::any())
            ->method('getName')
            ->will(self::returnValue('postgresql'));

        $this->databasePlatformMock
            ->expects(self::once())
            ->method('getIdentitySequenceName')
            ->with(self::identicalTo('table'), self::identicalTo('id'))
            ->will(self::returnValue('s_table_id'));

        $this->databaseConnectionMock
            ->expects(self::any())
            ->method('lastInsertId')
            ->with(self::identicalTo('s_table_id'))
            ->will(self::returnValue(43));

        self::assertSame(43, $this->connectionHelper->lastInsertId('table'));
    }
}
