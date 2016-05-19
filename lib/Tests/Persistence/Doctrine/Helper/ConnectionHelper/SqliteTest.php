<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;
use Netgen\BlockManager\Tests\Persistence\Doctrine\DatabaseTrait;

class SqliteTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite
     */
    protected $helper;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareDatabase(
            __DIR__ . '/../../../../_fixtures/schema',
            __DIR__ . '/../../../../_fixtures'
        );

        $this->helper = new Sqlite($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::getAutoIncrementValue
     */
    public function testGetAutoIncrementValue()
    {
        self::assertEquals(6, $this->helper->getAutoIncrementValue('ngbm_block'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::lastInsertId
     */
    public function testLastInsertId()
    {
        self::assertEquals(5, $this->helper->lastInsertId('ngbm_block'));
    }
}
