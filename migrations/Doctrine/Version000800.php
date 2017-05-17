<?php

namespace Netgen\BlockManager\Migrations\Doctrine;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version000800 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $blockTable = $schema->getTable('ngbm_block');
        $blockTable->addColumn('config', 'text', array('length' => 65535));
        $blockTable->dropColumn('placeholder_parameters');

        $collectionTable = $schema->getTable('ngbm_collection');
        $collectionTable->dropColumn('type');
        $collectionTable->dropColumn('shared');
        $collectionTable->dropColumn('name');

        $collectionQueryTable = $schema->getTable('ngbm_collection_query');
        $collectionQueryTable->dropColumn('position');
        $collectionQueryTable->dropColumn('identifier');

        $this->addSql('ALTER TABLE ngbm_layout ADD COLUMN description text NOT NULL AFTER name');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $blockTable = $schema->getTable('ngbm_block');
        $blockTable->addColumn('placeholder_parameters', 'text', array('length' => 65535));
        $blockTable->dropColumn('config');

        $collectionTable = $schema->getTable('ngbm_collection');
        $collectionTable->addColumn('type', 'integer');
        $collectionTable->addColumn('shared', 'boolean');
        $collectionTable->addColumn('name', 'string', array('length' => 255, 'notnull' => false));

        $layoutTable = $schema->getTable('ngbm_layout');
        $layoutTable->dropColumn('description');

        $collectionTable->addIndex(array('name'));

        $this->addSql('ALTER TABLE ngbm_collection_query ADD COLUMN position int(11) NOT NULL AFTER collection_id');
        $this->addSql('ALTER TABLE ngbm_collection_query ADD COLUMN identifier varchar(255) NOT NULL AFTER position');
    }
}
