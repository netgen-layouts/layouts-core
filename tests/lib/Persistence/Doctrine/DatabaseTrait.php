<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Configuration\YamlConfiguration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Exception\RuntimeException;

trait DatabaseTrait
{
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
     * @var \Doctrine\DBAL\Connection
     */
    private $databaseConnection;

    /**
     * Inserts database fixtures.
     */
    protected function insertDatabaseFixtures(string $fixturesPath): void
    {
        $data = require $fixturesPath . '/data.php';

        foreach ($data as $tableName => $tableData) {
            if (!empty($tableData)) {
                foreach ($tableData as $tableRow) {
                    $this->databaseConnection
                        ->createQueryBuilder()
                        ->insert($tableName)
                        ->values(array_fill_keys(array_keys($tableRow), '?'))
                        ->setParameters(
                            array_values($tableRow),
                            array_fill_keys(array_keys($tableRow), Type::STRING)
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
        if (is_string($databaseUri) && !empty($databaseUri)) {
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

        $useMigrations = getenv('USE_MIGRATIONS') === 'true';

        $schemaPath = rtrim($fixturesPath, '/') . '/schema';

        $useMigrations ?
            $this->executeMigrations() :
            $this->executeStatements($schemaPath);

        $this->insertDatabaseFixtures($fixturesPath);

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
            if (!empty($sqlQuery)) {
                $this->databaseConnection->query($sqlQuery);
            }
        }
    }

    /**
     * Creates the database schema from all available Doctrine migrations.
     */
    private function executeMigrations(): void
    {
        $configuration = new YamlConfiguration($this->databaseConnection);
        $configuration->load(__DIR__ . '/../../../../migrations/doctrine.yml');

        $migration = new Migration($configuration);
        $migration->migrate('0');
        $migration->migrate();
    }
}
