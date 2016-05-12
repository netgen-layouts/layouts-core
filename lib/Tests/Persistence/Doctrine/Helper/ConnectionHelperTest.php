<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;

class ConnectionHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $databasePlatformMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $databaseConnectionMock;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->databasePlatformMock = $this->getMock(AbstractPlatform::class);

        $this->databaseConnectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->databaseConnectionMock
            ->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnValue($this->databasePlatformMock));

        $this->connectionHelper = new ConnectionHelper($this->databaseConnectionMock);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::getAutoIncrementValue
     */
    public function testGetAutoIncrementValue()
    {
        $this->databasePlatformMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mysql'));

        self::assertEquals('null', $this->connectionHelper->getAutoIncrementValue('table'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::getAutoIncrementValue
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::getAutoIncrementValue
     */
    public function testGetAutoIncrementValueForPostgres()
    {
        $this->databasePlatformMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('postgresql'));

        $this->databasePlatformMock
            ->expects($this->once())
            ->method('getIdentitySequenceName')
            ->with($this->equalTo('table'), $this->equalTo('id'))
            ->will($this->returnValue('s_table_id'));

        self::assertEquals("nextval('s_table_id')", $this->connectionHelper->getAutoIncrementValue('table'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::lastInsertId
     */
    public function testLastInsertId()
    {
        $this->databasePlatformMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mysql'));

        $this->databaseConnectionMock
            ->expects($this->any())
            ->method('lastInsertId')
            ->with($this->equalTo('table'))
            ->will($this->returnValue(42));

        self::assertEquals(42, $this->connectionHelper->lastInsertId('table'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper::lastInsertId
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres::lastInsertId
     */
    public function testLastInsertIdForPostgres()
    {
        $this->databasePlatformMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('postgresql'));

        $this->databasePlatformMock
            ->expects($this->once())
            ->method('getIdentitySequenceName')
            ->with($this->equalTo('table'), $this->equalTo('id'))
            ->will($this->returnValue('s_table_id'));

        $this->databaseConnectionMock
            ->expects($this->any())
            ->method('lastInsertId')
            ->with($this->equalTo('s_table_id'))
            ->will($this->returnValue(43));

        self::assertEquals(43, $this->connectionHelper->lastInsertId('table'));
    }
}
