<?php

namespace Netgen\BlockManager\Migrations\Doctrine;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160505141919 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $platformName = $this->platform->getName();

        // ngbm_collection table

        $collectionTable = $schema->createTable('ngbm_collection');

        $collectionTable->addColumn('id', 'integer', array('autoincrement' => $platformName !== 'sqlite'));
        $collectionTable->addColumn('status', 'integer');
        $collectionTable->addColumn('type', 'integer');
        $collectionTable->addColumn('name', 'string', array('length' => 255, 'notnull' => false));

        $collectionTable->setPrimaryKey(array('id', 'status'));

        // ngbm_collection_item table

        $collectionItemTable = $schema->createTable('ngbm_collection_item');

        $collectionItemTable->addColumn('id', 'integer', array('autoincrement' => $platformName !== 'sqlite'));
        $collectionItemTable->addColumn('status', 'integer');
        $collectionItemTable->addColumn('collection_id', 'integer');
        $collectionItemTable->addColumn('position', 'integer');
        $collectionItemTable->addColumn('type', 'integer');
        $collectionItemTable->addColumn('value_id', 'string', array('length' => 255));
        $collectionItemTable->addColumn('value_type', 'string', array('length' => 255));

        $collectionItemTable->setPrimaryKey(array('id', 'status'));

        // ngbm_collection_query table

        $collectionQueryTable = $schema->createTable('ngbm_collection_query');

        $collectionQueryTable->addColumn('id', 'integer', array('autoincrement' => $platformName !== 'sqlite'));
        $collectionQueryTable->addColumn('status', 'integer');
        $collectionQueryTable->addColumn('collection_id', 'integer');
        $collectionQueryTable->addColumn('position', 'integer');
        $collectionQueryTable->addColumn('identifier', 'string', array('length' => 255));
        $collectionQueryTable->addColumn('type', 'string', array('length' => 255));
        $collectionQueryTable->addColumn('parameters', 'text', array('length' => 65535));

        $collectionQueryTable->setPrimaryKey(array('id', 'status'));
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('ngbm_collection');
        $schema->dropTable('ngbm_collection_item');
        $schema->dropTable('ngbm_collection_query');
    }
}
