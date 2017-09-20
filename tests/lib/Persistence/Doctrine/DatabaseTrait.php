<?php

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
    private $inMemoryDsn = 'sqlite://:memory:';

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
     * Sets up the database connection.
     *
     * @param string $fixturesPath
     */
    private function createDatabase($fixturesPath = __DIR__ . '/../../../_fixtures')
    {
        $this->databaseUri = getenv('DATABASE');
        if (empty($this->databaseUri)) {
            $this->databaseUri = $this->inMemoryDsn;
        }

        $useMigrations = getenv('USE_MIGRATIONS') === 'true';

        $schemaPath = rtrim($fixturesPath, '/') . '/schema';

        preg_match('/^(?<db>.+):\/\//', $this->databaseUri, $matches);
        $this->databaseServer = $matches['db'];

        $this->databaseConnection = DriverManager::getConnection(
            array(
                'url' => $this->databaseUri,
            )
        );

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
    private function closeDatabase()
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
    private function executeStatements($schemaPath, $fileName = 'schema')
    {
        $fullPath = $schemaPath . '/' . $fileName . '.' . $this->databaseServer . '.sql';
        if (!file_exists($fullPath)) {
            throw new RuntimeException("File '{$fullPath}' does not exist.");
        }

        $schema = file_get_contents($fullPath);
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
    private function executeMigrations()
    {
        $configuration = new YamlConfiguration($this->databaseConnection);
        $configuration->load(__DIR__ . '/../../../../migrations/doctrine.yml');

        $migration = new Migration($configuration);
        $migration->migrate(0);
        $migration->migrate();
    }

    /**
     * Inserts database fixtures.
     *
     * @param string $fixturesPath
     */
    private function insertDatabaseFixtures($fixturesPath)
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
