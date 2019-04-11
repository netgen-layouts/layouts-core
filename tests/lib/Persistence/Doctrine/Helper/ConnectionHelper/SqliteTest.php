<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class SqliteTest extends TestCase
{
    use DatabaseTrait;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite
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
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::getAutoIncrementValue
     */
    public function testGetAutoIncrementValue(): void
    {
        self::assertSame(39, $this->helper->getAutoIncrementValue('ngbm_block'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::lastInsertId
     */
    public function testLastInsertId(): void
    {
        self::assertSame(38, $this->helper->lastInsertId('ngbm_block'));
    }
}
