<?php

namespace Netgen\BlockManager\Migrations\Doctrine;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version010000 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // ngbm_layout table

        $layoutTable = $schema->createTable('ngbm_layout');

        $layoutTable->addColumn('id', 'integer', array('autoincrement' => true));
        $layoutTable->addColumn('status', 'integer');
        $layoutTable->addColumn('type', 'string', array('length' => 255));
        $layoutTable->addColumn('name', 'string', array('length' => 255));
        $layoutTable->addColumn('created', 'integer');
        $layoutTable->addColumn('modified', 'integer');

        $layoutTable->setPrimaryKey(array('id', 'status'));

        // ngbm_zone table

        $zoneTable = $schema->createTable('ngbm_zone');

        $zoneTable->addColumn('identifier', 'string', array('length' => 255));
        $zoneTable->addColumn('layout_id', 'integer');
        $zoneTable->addColumn('status', 'integer');

        $zoneTable->setPrimaryKey(array('identifier', 'layout_id', 'status'));
        $zoneTable->addForeignKeyConstraint('ngbm_layout', array('layout_id', 'status'), array('id', 'status'));

        // ngbm_block table

        $blockTable = $schema->createTable('ngbm_block');

        $blockTable->addColumn('id', 'integer', array('autoincrement' => true));
        $blockTable->addColumn('status', 'integer');
        $blockTable->addColumn('layout_id', 'integer');
        $blockTable->addColumn('zone_identifier', 'string', array('length' => 255));
        $blockTable->addColumn('position', 'integer');
        $blockTable->addColumn('definition_identifier', 'string', array('length' => 255));
        $blockTable->addColumn('view_type', 'string', array('length' => 255));
        $blockTable->addColumn('item_view_type', 'string', array('length' => 255));
        $blockTable->addColumn('name', 'string', array('length' => 255));
        $blockTable->addColumn('parameters', 'text', array('length' => 65535));

        $blockTable->setPrimaryKey(array('id', 'status'));
        $blockTable->addForeignKeyConstraint('ngbm_layout', array('layout_id', 'status'), array('id', 'status'));

        // ngbm_rule table

        $ruleTable = $schema->createTable('ngbm_rule');

        $ruleTable->addColumn('id', 'integer', array('autoincrement' => true));
        $ruleTable->addColumn('status', 'integer');
        $ruleTable->addColumn('layout_id', 'integer', array('notnull' => false));
        $ruleTable->addColumn('priority', 'integer');
        $ruleTable->addColumn('comment', 'string', array('length' => 255, 'notnull' => false));

        $ruleTable->setPrimaryKey(array('id', 'status'));

        // ngbm_rule_data table

        $ruleDataTable = $schema->createTable('ngbm_rule_data');

        $ruleDataTable->addColumn('rule_id', 'integer');
        $ruleDataTable->addColumn('enabled', 'boolean');

        $ruleDataTable->setPrimaryKey(array('rule_id'));

        // ngbm_rule_target table

        $ruleTargetTable = $schema->createTable('ngbm_rule_target');

        $ruleTargetTable->addColumn('id', 'integer', array('autoincrement' => true));
        $ruleTargetTable->addColumn('status', 'integer');
        $ruleTargetTable->addColumn('rule_id', 'integer');
        $ruleTargetTable->addColumn('identifier', 'string', array('length' => 255));
        $ruleTargetTable->addColumn('value', 'text', array('length' => 65535));

        $ruleTargetTable->setPrimaryKey(array('id', 'status'));
        $ruleTargetTable->addForeignKeyConstraint('ngbm_rule', array('rule_id', 'status'), array('id', 'status'));

        // ngbm_rule_condition table

        $ruleConditionTable = $schema->createTable('ngbm_rule_condition');

        $ruleConditionTable->addColumn('id', 'integer', array('autoincrement' => true));
        $ruleConditionTable->addColumn('status', 'integer');
        $ruleConditionTable->addColumn('rule_id', 'integer');
        $ruleConditionTable->addColumn('identifier', 'string', array('length' => 255));
        $ruleConditionTable->addColumn('value', 'text', array('length' => 65535));

        $ruleConditionTable->setPrimaryKey(array('id', 'status'));
        $ruleConditionTable->addForeignKeyConstraint('ngbm_rule', array('rule_id', 'status'), array('id', 'status'));

        // ngbm_collection table

        $collectionTable = $schema->createTable('ngbm_collection');

        $collectionTable->addColumn('id', 'integer', array('autoincrement' => true));
        $collectionTable->addColumn('status', 'integer');
        $collectionTable->addColumn('type', 'integer');
        $collectionTable->addColumn('name', 'string', array('length' => 255, 'notnull' => false));

        $collectionTable->setPrimaryKey(array('id', 'status'));

        // ngbm_collection_item table

        $collectionItemTable = $schema->createTable('ngbm_collection_item');

        $collectionItemTable->addColumn('id', 'integer', array('autoincrement' => true));
        $collectionItemTable->addColumn('status', 'integer');
        $collectionItemTable->addColumn('collection_id', 'integer');
        $collectionItemTable->addColumn('position', 'integer');
        $collectionItemTable->addColumn('type', 'integer');
        $collectionItemTable->addColumn('value_id', 'string', array('length' => 255));
        $collectionItemTable->addColumn('value_type', 'string', array('length' => 255));

        $collectionItemTable->setPrimaryKey(array('id', 'status'));
        $collectionItemTable->addForeignKeyConstraint('ngbm_collection', array('collection_id', 'status'), array('id', 'status'));

        // ngbm_collection_query table

        $collectionQueryTable = $schema->createTable('ngbm_collection_query');

        $collectionQueryTable->addColumn('id', 'integer', array('autoincrement' => true));
        $collectionQueryTable->addColumn('status', 'integer');
        $collectionQueryTable->addColumn('collection_id', 'integer');
        $collectionQueryTable->addColumn('position', 'integer');
        $collectionQueryTable->addColumn('identifier', 'string', array('length' => 255));
        $collectionQueryTable->addColumn('type', 'string', array('length' => 255));
        $collectionQueryTable->addColumn('parameters', 'text', array('length' => 65535));

        $collectionQueryTable->setPrimaryKey(array('id', 'status'));
        $collectionQueryTable->addForeignKeyConstraint('ngbm_collection', array('collection_id', 'status'), array('id', 'status'));

        // ngbm_block_collection table

        $blockCollectionTable = $schema->createTable('ngbm_block_collection');

        $blockCollectionTable->addColumn('block_id', 'integer');
        $blockCollectionTable->addColumn('block_status', 'integer');
        $blockCollectionTable->addColumn('collection_id', 'integer');
        $blockCollectionTable->addColumn('collection_status', 'integer');
        $blockCollectionTable->addColumn('identifier', 'string', array('length' => 255));
        $blockCollectionTable->addColumn('start', 'integer');
        $blockCollectionTable->addColumn('length', 'integer', array('notnull' => false));

        $blockCollectionTable->setPrimaryKey(array('block_id', 'block_status', 'identifier'));
        $blockCollectionTable->addForeignKeyConstraint('ngbm_block', array('block_id', 'block_status'), array('id', 'status'));
        $blockCollectionTable->addForeignKeyConstraint('ngbm_collection', array('collection_id', 'collection_status'), array('id', 'status'));
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('ngbm_block_collection');
        $schema->dropTable('ngbm_collection_item');
        $schema->dropTable('ngbm_collection_query');
        $schema->dropTable('ngbm_collection');

        $schema->dropTable('ngbm_block');
        $schema->dropTable('ngbm_zone');
        $schema->dropTable('ngbm_layout');

        $schema->dropTable('ngbm_rule_target');
        $schema->dropTable('ngbm_rule_condition');
        $schema->dropTable('ngbm_rule_data');
        $schema->dropTable('ngbm_rule');
    }
}
