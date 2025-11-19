<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sqlite::class)]
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

    public function testNextId(): void
    {
        if (!$this->databaseConnection->getDatabasePlatform() instanceof SqlitePlatform) {
            self::markTestSkipped('Test only runs on Sqlite.');
        }

        self::assertSame('39', $this->helper->nextId('nglayouts_block'));
    }

    public function testLastId(): void
    {
        if (!$this->databaseConnection->getDatabasePlatform() instanceof SqlitePlatform) {
            self::markTestSkipped('Test only runs on Sqlite.');
        }

        self::assertSame(38, $this->helper->lastId('nglayouts_block'));
    }
}
