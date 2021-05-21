<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

final class Version000700 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        // ngbm_layout table

        $layoutTable = $schema->createTable('ngbm_layout');

        $layoutTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $layoutTable->addColumn('status', 'integer');
        $layoutTable->addColumn('type', 'string', ['length' => 191]);
        $layoutTable->addColumn('name', 'string', ['length' => 191]);
        $layoutTable->addColumn('created', 'integer');
        $layoutTable->addColumn('modified', 'integer');
        $layoutTable->addColumn('shared', 'boolean');

        $layoutTable->setPrimaryKey(['id', 'status']);

        $layoutTable->addIndex(['name'], 'idx_ngl_layout_name');

        // ngbm_block table

        $blockTable = $schema->createTable('ngbm_block');

        $blockTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $blockTable->addColumn('status', 'integer');
        $blockTable->addColumn('layout_id', 'integer');
        $blockTable->addColumn('depth', 'integer');
        $blockTable->addColumn('path', 'string', ['length' => 191]);
        $blockTable->addColumn('parent_id', 'integer', ['notnull' => false]);
        $blockTable->addColumn('placeholder', 'string', ['length' => 191, 'notnull' => false]);
        $blockTable->addColumn('position', 'integer', ['notnull' => false]);
        $blockTable->addColumn('definition_identifier', 'string', ['length' => 191]);
        $blockTable->addColumn('view_type', 'string', ['length' => 191]);
        $blockTable->addColumn('item_view_type', 'string', ['length' => 191]);
        $blockTable->addColumn('name', 'string', ['length' => 191]);
        $blockTable->addColumn('placeholder_parameters', 'text', ['length' => 65535]);
        $blockTable->addColumn('parameters', 'text', ['length' => 65535]);

        $blockTable->setPrimaryKey(['id', 'status']);
        $blockTable->addForeignKeyConstraint('ngbm_layout', ['layout_id', 'status'], ['id', 'status'], [], 'fk_ngl_block_layout');

        $blockTable->addIndex(['layout_id', 'status'], 'idx_ngl_layout');
        $blockTable->addIndex(['parent_id', 'placeholder', 'status'], 'idx_ngl_parent_block');

        // ngbm_zone table

        $zoneTable = $schema->createTable('ngbm_zone');

        $zoneTable->addColumn('identifier', 'string', ['length' => 191]);
        $zoneTable->addColumn('layout_id', 'integer');
        $zoneTable->addColumn('status', 'integer');
        $zoneTable->addColumn('root_block_id', 'integer');
        $zoneTable->addColumn('linked_layout_id', 'integer', ['notnull' => false]);
        $zoneTable->addColumn('linked_zone_identifier', 'string', ['length' => 191, 'notnull' => false]);

        $zoneTable->setPrimaryKey(['identifier', 'layout_id', 'status']);
        $zoneTable->addForeignKeyConstraint('ngbm_layout', ['layout_id', 'status'], ['id', 'status'], [], 'fk_ngl_zone_layout');
        $zoneTable->addForeignKeyConstraint('ngbm_block', ['root_block_id', 'status'], ['id', 'status'], [], 'fk_ngl_zone_block');

        $zoneTable->addIndex(['layout_id', 'status'], 'idx_ngl_layout');
        $zoneTable->addIndex(['root_block_id', 'status'], 'idx_ngl_root_block');

        // ngbm_rule table

        $ruleTable = $schema->createTable('ngbm_rule');

        $ruleTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $ruleTable->addColumn('status', 'integer');
        $ruleTable->addColumn('layout_id', 'integer', ['notnull' => false]);
        $ruleTable->addColumn('comment', 'string', ['length' => 191, 'notnull' => false]);

        $ruleTable->setPrimaryKey(['id', 'status']);

        $ruleTable->addIndex(['layout_id'], 'idx_ngl_related_layout');

        // ngbm_rule_data table

        $ruleDataTable = $schema->createTable('ngbm_rule_data');

        $ruleDataTable->addColumn('rule_id', 'integer');
        $ruleDataTable->addColumn('enabled', 'boolean');
        $ruleDataTable->addColumn('priority', 'integer');

        $ruleDataTable->setPrimaryKey(['rule_id']);

        // ngbm_rule_target table

        $ruleTargetTable = $schema->createTable('ngbm_rule_target');

        $ruleTargetTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $ruleTargetTable->addColumn('status', 'integer');
        $ruleTargetTable->addColumn('rule_id', 'integer');
        $ruleTargetTable->addColumn('type', 'string', ['length' => 191]);
        $ruleTargetTable->addColumn('value', 'text', ['length' => 65535, 'notnull' => false]);

        $ruleTargetTable->setPrimaryKey(['id', 'status']);
        $ruleTargetTable->addForeignKeyConstraint('ngbm_rule', ['rule_id', 'status'], ['id', 'status'], [], 'fk_ngl_target_rule');

        $ruleTargetTable->addIndex(['rule_id', 'status'], 'idx_ngl_rule');
        $ruleTargetTable->addIndex(['type'], 'idx_ngl_target_type');

        // ngbm_rule_condition table

        $ruleConditionTable = $schema->createTable('ngbm_rule_condition');

        $ruleConditionTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $ruleConditionTable->addColumn('status', 'integer');
        $ruleConditionTable->addColumn('rule_id', 'integer');
        $ruleConditionTable->addColumn('type', 'string', ['length' => 191]);
        $ruleConditionTable->addColumn('value', 'text', ['length' => 65535, 'notnull' => false]);

        $ruleConditionTable->setPrimaryKey(['id', 'status']);
        $ruleConditionTable->addForeignKeyConstraint('ngbm_rule', ['rule_id', 'status'], ['id', 'status'], [], 'fk_ngl_condition_rule');

        $ruleConditionTable->addIndex(['rule_id', 'status'], 'idx_ngl_rule');

        // ngbm_collection table

        $collectionTable = $schema->createTable('ngbm_collection');

        $collectionTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $collectionTable->addColumn('status', 'integer');
        $collectionTable->addColumn('type', 'integer');
        $collectionTable->addColumn('shared', 'boolean');
        $collectionTable->addColumn('name', 'string', ['length' => 191, 'notnull' => false]);

        $collectionTable->setPrimaryKey(['id', 'status']);

        $collectionTable->addIndex(['name'], 'idx_ngl_collection_name');

        // ngbm_collection_item table

        $collectionItemTable = $schema->createTable('ngbm_collection_item');

        $collectionItemTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $collectionItemTable->addColumn('status', 'integer');
        $collectionItemTable->addColumn('collection_id', 'integer');
        $collectionItemTable->addColumn('position', 'integer');
        $collectionItemTable->addColumn('type', 'integer');
        $collectionItemTable->addColumn('value_id', 'string', ['length' => 191]);
        $collectionItemTable->addColumn('value_type', 'string', ['length' => 191]);

        $collectionItemTable->setPrimaryKey(['id', 'status']);
        $collectionItemTable->addForeignKeyConstraint('ngbm_collection', ['collection_id', 'status'], ['id', 'status'], [], 'fk_ngl_item_collection');

        $collectionItemTable->addIndex(['collection_id', 'status'], 'idx_ngl_collection');

        // ngbm_collection_query table

        $collectionQueryTable = $schema->createTable('ngbm_collection_query');

        $collectionQueryTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $collectionQueryTable->addColumn('status', 'integer');
        $collectionQueryTable->addColumn('collection_id', 'integer');
        $collectionQueryTable->addColumn('position', 'integer');
        $collectionQueryTable->addColumn('identifier', 'string', ['length' => 191]);
        $collectionQueryTable->addColumn('type', 'string', ['length' => 191]);
        $collectionQueryTable->addColumn('parameters', 'text', ['length' => 65535]);

        $collectionQueryTable->setPrimaryKey(['id', 'status']);
        $collectionQueryTable->addForeignKeyConstraint('ngbm_collection', ['collection_id', 'status'], ['id', 'status'], [], 'fk_ngl_query_collection');

        $collectionQueryTable->addIndex(['collection_id', 'status'], 'idx_ngl_collection');

        // ngbm_block_collection table

        $blockCollectionTable = $schema->createTable('ngbm_block_collection');

        $blockCollectionTable->addColumn('block_id', 'integer');
        $blockCollectionTable->addColumn('block_status', 'integer');
        $blockCollectionTable->addColumn('collection_id', 'integer');
        $blockCollectionTable->addColumn('collection_status', 'integer');
        $blockCollectionTable->addColumn('identifier', 'string', ['length' => 191]);
        $blockCollectionTable->addColumn('start', 'integer');
        $blockCollectionTable->addColumn('length', 'integer', ['notnull' => false]);

        $blockCollectionTable->setPrimaryKey(['block_id', 'block_status', 'identifier']);
        $blockCollectionTable->addForeignKeyConstraint('ngbm_block', ['block_id', 'block_status'], ['id', 'status'], [], 'fk_ngl_block_collection_block');
        $blockCollectionTable->addForeignKeyConstraint('ngbm_collection', ['collection_id', 'collection_status'], ['id', 'status'], [], 'fk_ngl_block_collection_collection');

        $blockCollectionTable->addIndex(['block_id', 'block_status'], 'idx_ngl_block');
        $blockCollectionTable->addIndex(['collection_id', 'collection_status'], 'idx_ngl_collection');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $schema->dropTable('ngbm_block_collection');
        $schema->dropTable('ngbm_collection_item');
        $schema->dropTable('ngbm_collection_query');
        $schema->dropTable('ngbm_collection');

        $schema->dropTable('ngbm_zone');
        $schema->dropTable('ngbm_block');
        $schema->dropTable('ngbm_layout');

        $schema->dropTable('ngbm_rule_target');
        $schema->dropTable('ngbm_rule_condition');
        $schema->dropTable('ngbm_rule_data');
        $schema->dropTable('ngbm_rule');
    }
}
