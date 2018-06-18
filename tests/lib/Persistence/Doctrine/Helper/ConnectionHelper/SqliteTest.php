<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;
use Netgen\BlockManager\Tests\Persistence\Doctrine\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class SqliteTest extends TestCase
{
    use DatabaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite
     */
    private $helper;

    public function setUp(): void
    {
        $this->createDatabase(__DIR__ . '/../../../../../_fixtures');

        $this->helper = new Sqlite($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::getAutoIncrementValue
     */
    public function testGetAutoIncrementValue(): void
    {
        $this->assertSame(39, $this->helper->getAutoIncrementValue('ngbm_block'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::lastInsertId
     */
    public function testLastInsertId(): void
    {
        $this->assertSame(38, $this->helper->lastInsertId('ngbm_block'));
    }
}
