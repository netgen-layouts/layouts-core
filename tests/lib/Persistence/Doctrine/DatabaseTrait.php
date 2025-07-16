<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
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
use function preg_match;
use function rtrim;
use function sprintf;

trait DatabaseTrait
{
    private Connection $databaseConnection;

    private string $inMemoryDsn = 'sqlite:///:memory:';

    private string $databaseUri;

    private string $databaseServer;

    protected function insertDatabaseFixtures(string $fixturesPath): void
    {
        foreach (require $fixturesPath as $tableName => $tableData) {
            if (count($tableData) > 0) {
                foreach ($tableData as $tableRow) {
                    $this->databaseConnection
                        ->createQueryBuilder()
                        ->insert($tableName)
                        ->values(array_fill_keys(array_keys($tableRow), '?'))
                        ->setParameters(
                            array_values($tableRow),
                            array_fill_keys(array_keys($tableRow), Types::STRING),
                        )
                        ->execute();
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

        $this->databaseConnection = DriverManager::getConnection(
            [
                'url' => $this->databaseUri,
            ],
        );

        return $this->databaseConnection;
    }

    private function createDatabase(string $fixturesPath = __DIR__ . '/../../../_fixtures'): void
    {
        if (!isset($this->databaseConnection)) {
            $this->createDatabaseConnection();
        }

        $schemaPath = rtrim($fixturesPath, '/') . '/schema';

        $this->executeStatements($schemaPath);
        $this->insertDatabaseFixtures($fixturesPath . '/data.php');

        if ($this->databaseServer === 'pgsql') {
            $this->executeStatements($schemaPath, 'setval');
        }
    }

    private function closeDatabase(): void
    {
        if ($this->databaseUri !== $this->inMemoryDsn) {
            $this->databaseConnection->close();
        }
    }

    private function executeStatements(string $filePath, string $fileName = 'schema'): void
    {
        $fullPath = sprintf('%s/%s.%s.sql', $filePath, $fileName, $this->databaseServer);
        if (!file_exists($fullPath)) {
            throw new RuntimeException(sprintf('File "%s" does not exist.', $fullPath));
        }

        $schema = file_get_contents($fullPath);
        if (!is_string($schema)) {
            throw new RuntimeException(sprintf('File "%s" is not readable.', $fullPath));
        }

        foreach (array_map('trim', explode(';', $schema)) as $sqlQuery) {
            if ($sqlQuery !== '') {
                $this->databaseConnection->executeQuery($sqlQuery);
            }
        }
    }
}
