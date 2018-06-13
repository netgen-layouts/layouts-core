<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

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
    protected $inMemoryDsn = 'sqlite:///:memory:';

    /**
     * @var string
     */
    protected $databaseUri;

    /**
     * @var string
     */
    protected $databaseServer;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    /**
     * Sets up the database connection.
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function createDatabaseConnection()
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
     *
     * @param string $fixturesPath
     */
    protected function createDatabase($fixturesPath = __DIR__ . '/../../../_fixtures')
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
    protected function closeDatabase()
    {
        if ($this->databaseUri !== $this->inMemoryDsn) {
            $this->databaseConnection->close();
        }
    }

    /**
     * Creates the database schema.
     *
     * @param string $schemaPath
     * @param string $fileName
     */
    protected function executeStatements($schemaPath, $fileName = 'schema')
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
    protected function executeMigrations()
    {
        $configuration = new YamlConfiguration($this->databaseConnection);
        $configuration->load(__DIR__ . '/../../../../migrations/doctrine.yml');

        $migration = new Migration($configuration);
        $migration->migrate('0');
        $migration->migrate();
    }

    /**
     * Inserts database fixtures.
     *
     * @param string $fixturesPath
     */
    protected function insertDatabaseFixtures($fixturesPath)
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
}
