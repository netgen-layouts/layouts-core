<?php

namespace Netgen\BlockManager\Tests\Traits;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;

trait DoctrineDatabaseTrait
{
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
        $databaseUri = getenv('DATABASE');
        if (empty($databaseUri)) {
            $databaseUri = 'sqlite://:memory:';
        }

        $this->databaseUri = $databaseUri;

        preg_match('/^(?<db>.+):\/\//', $this->databaseUri, $matches);
        $this->databaseServer = $matches['db'];

        $this->createDatabaseConnection();
        $this->createDatabaseSchema($schemaPath);
        $this->insertDatabaseFixtures($fixturesPath);
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
     * Creates the database schema.
     *
     * @param string $schemaPath
     */
    protected function createDatabaseSchema($schemaPath)
    {
        $schema = file_get_contents($schemaPath . '/schema.' . $this->databaseServer . '.sql');
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
