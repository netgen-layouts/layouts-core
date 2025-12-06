<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\RuntimeException;

use function array_fill_keys;
use function array_keys;
use function array_map;
use function array_values;
use function count;
use function explode;
use function file_exists;
use function file_get_contents;
use function getenv;
use function is_string;
use function mb_rtrim;
use function preg_match;
use function sprintf;

trait DatabaseTrait
{
    final protected Connection $databaseConnection;

    private string $inMemoryDsn = 'sqlite:///:memory:';

    private string $databaseUri;

    private string $databaseServer;

    protected function provideFixturesPath(): string
    {
        return __DIR__ . '/../../../_fixtures';
    }

    private function insertDatabaseFixtures(): void
    {
        /** @var array<string, array<int, array<string, mixed>>> $data */
        $data = require mb_rtrim($this->provideFixturesPath(), '/') . '/data.php';

        foreach ($data as $tableName => $tableData) {
            if (count($tableData) > 0) {
                foreach ($tableData as $tableRow) {
                    $values = array_fill_keys(array_keys($tableRow), '?');
                    $parameters = array_fill_keys(array_keys($tableRow), Types::STRING);

                    $this->databaseConnection
                        ->createQueryBuilder()
                        ->insert($tableName)
                        ->values($values)
                        ->setParameters(array_values($tableRow), $parameters)
                        ->executeStatement();
                }
            }
        }
    }

    private function createDatabaseConnection(): Connection
    {
        $this->databaseServer = 'sqlite';
        $this->databaseUri = $this->inMemoryDsn;

        $databaseUri = getenv('DATABASE');
        if (is_string($databaseUri) && $databaseUri !== '') {
            preg_match('/^(?<db>.+):\/+/', $databaseUri, $matches);

            if (isset($matches['db'])) {
                $this->databaseServer = $matches['db'];
                $this->databaseUri = $databaseUri;
            }
        }

        $dsnParser = new DsnParser([$this->databaseServer => 'pdo_' . $this->databaseServer]);
        $this->databaseConnection = DriverManager::getConnection(
            $dsnParser->parse($this->databaseUri),
        );

        return $this->databaseConnection;
    }

    private function createDatabase(): void
    {
        if (!isset($this->databaseConnection)) {
            $this->createDatabaseConnection();
        }

        $this->executeStatements();
        $this->insertDatabaseFixtures();

        if ($this->databaseServer === 'pgsql') {
            $this->executeStatements('setval');
        }
    }

    private function closeDatabase(): void
    {
        if ($this->databaseUri !== $this->inMemoryDsn) {
            $this->databaseConnection->close();
        }
    }

    private function executeStatements(string $fileName = 'schema'): void
    {
        $schemaPath = __DIR__ . '/../../../_fixtures/schema';
        $fullPath = sprintf('%s/%s.%s.sql', $schemaPath, $fileName, $this->databaseServer);

        if (!file_exists($fullPath)) {
            throw new RuntimeException(sprintf('File "%s" does not exist.', $fullPath));
        }

        $schema = file_get_contents($fullPath);
        if (!is_string($schema)) {
            throw new RuntimeException(sprintf('File "%s" is not readable.', $fullPath));
        }

        foreach (array_map('mb_trim', explode(';', $schema)) as $sqlQuery) {
            if ($sqlQuery !== '') {
                $this->databaseConnection->executeQuery($sqlQuery);
            }
        }
    }
}
