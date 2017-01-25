<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;
use Netgen\BlockManager\Tests\Persistence\Doctrine\DatabaseTrait;
use PHPUnit\Framework\TestCase;

class SqliteTest extends TestCase
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
        $this->createDatabase(__DIR__ . '/../../../../_fixtures');

        $this->helper = new Sqlite($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::getAutoIncrementValue
     */
    public function testGetAutoIncrementValue()
    {
        $this->assertEquals(7, $this->helper->getAutoIncrementValue('ngbm_block'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::lastInsertId
     */
    public function testLastInsertId()
    {
        $this->assertEquals(6, $this->helper->lastInsertId('ngbm_block'));
    }
}