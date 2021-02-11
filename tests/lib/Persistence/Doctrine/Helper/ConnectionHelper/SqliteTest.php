<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class SqliteTest extends TestCase
{
    use DatabaseTrait;

    private Sqlite $helper;

    protected function setUp(): void
    {
        $this->createDatabase();

        $this->helper = new Sqlite($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    protected function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::nextId
     */
    public function testNextId(): void
    {
        self::assertSame(39, $this->helper->nextId('nglayouts_block'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite::lastId
     */
    public function testLastId(): void
    {
        self::assertSame(38, $this->helper->lastId('nglayouts_block'));
    }
}
