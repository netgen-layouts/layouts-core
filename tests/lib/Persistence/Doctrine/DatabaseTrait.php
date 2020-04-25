<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\RuntimeException;
use function array_fill_keys;
use function array_keys;
use function array_values;
use function count;
use function explode;
use function file_exists;
use function file_get_contents;
use function getenv;
use function is_string;
use function preg_match;
use function rtrim;
use function trim;

trait DatabaseTrait
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;
    /**
     * @var string
     */
    private $inMemoryDsn = 'sqlite:///:memory:';

    /**
     * @var string
     */
    private $databaseUri;

    /**
     * @var string
     */
    private $databaseServer;

    /**
     * Inserts database fixtures.
     */
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
                            array_fill_keys(array_keys($tableRow), Types::STRING)
                        )
                        ->execute();
                }
            }
        }
    }

    /**
     * Sets up the database connection.
     */
    private function createDatabaseConnection(): Connection
    {
        $this->databaseUri = $this->inMemoryDsn;

        $databaseUri = getenv('DATABASE');
        if (is_string($databaseUri) && $databaseUri !== '') {
            $this->databaseUri = $databaseUri;
        }

        preg_match('/^(?<db>.+):\/+/', $this->databaseUri, $matches);
        $this->databaseServer = $matches['db'];

        $this->databaseConnection = DriverManager::getConnection(
            [
                'url' => $this->databaseUri,
            ]
        );

        return $this->databaseConnection;
    }

    /**
     * Sets up the database connection.
     */
    private function createDatabase(string $fixturesPath = __DIR__ . '/../../../_fixtures'): void
    {
        if ($this->databaseConnection === null) {
            $this->createDatabaseConnection();
        }

        $schemaPath = rtrim($fixturesPath, '/') . '/schema';

        $this->executeStatements($schemaPath);

        $this->insertDatabaseFixtures($fixturesPath . '/data.php');

        if ($this->databaseServer === 'pgsql') {
            $this->executeStatements($schemaPath, 'setval');
        }
    }

    /**
     * Closes the database connection.
     */
    private function closeDatabase(): void
    {
        if ($this->databaseUri !== $this->inMemoryDsn) {
            $this->databaseConnection->close();
        }
    }

    /**
     * Creates the database schema.
     */
    private function executeStatements(string $schemaPath, string $fileName = 'schema'): void
    {
        $fullPath = $schemaPath . '/' . $fileName . '.' . $this->databaseServer . '.sql';
        if (!file_exists($fullPath)) {
            throw new RuntimeException("File '{$fullPath}' does not exist.");
        }

        $schema = file_get_contents($fullPath);
        if (!is_string($schema)) {
            throw new RuntimeException("File '{$fullPath}' is not readable.");
        }

        $sqlQueries = explode(';', $schema);

        foreach ($sqlQueries as $sqlQuery) {
            $sqlQuery = trim($sqlQuery);
            if ($sqlQuery !== '') {
                $this->databaseConnection->query($sqlQuery);
            }
        }
    }
}
