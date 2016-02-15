<?php

namespace Netgen\BlockManager\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;

trait DoctrineDatabaseTrait
{
    /**
     * @var string
     */
    protected $inMemoryDsn = 'sqlite://:memory:';

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
     * @param string $schemaPath
     * @param string $fixturesPath
     */
    protected function prepareDatabase($schemaPath, $fixturesPath)
    {
        $this->databaseUri = getenv('DATABASE');
        if (empty($this->databaseUri)) {
            $this->databaseUri = $this->inMemoryDsn;
        }

        preg_match('/^(?<db>.+):\/\//', $this->databaseUri, $matches);
        $this->databaseServer = $matches['db'];

        $this->createDatabaseConnection();
        $this->executeStatements($schemaPath);
        $this->insertDatabaseFixtures($fixturesPath);
        $this->executeStatements($schemaPath, 'setval');
    }

    /**
     * Creates the database connection.
     */
    protected function createDatabaseConnection()
    {
        $this->databaseConnection = DriverManager::getConnection(
            array(
                'url' => $this->databaseUri,
            )
        );
    }

    /**
     * Closes the database connection.
     */
    protected function closeDatabaseConnection()
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
            return;
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
