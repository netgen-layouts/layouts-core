<?php

namespace Netgen\BlockManager\Migrations\Doctrine;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160211144645 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $platformName = $this->platform->getName();

        // ngbm_layout table

        $layoutTable = $schema->createTable('ngbm_layout');

        $layoutTable->addColumn('id', 'integer', array('autoincrement' => $platformName !== 'sqlite'));
        $layoutTable->addColumn('status', 'integer');
        $layoutTable->addColumn('parent_id', 'integer', array('notnull' => false));
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

        $blockTable->addColumn('id', 'integer', array('autoincrement' => $platformName !== 'sqlite'));
        $blockTable->addColumn('status', 'integer');
        $blockTable->addColumn('layout_id', 'integer');
        $blockTable->addColumn('zone_identifier', 'string', array('length' => 255));
        $blockTable->addColumn('position', 'integer');
        $blockTable->addColumn('definition_identifier', 'string', array('length' => 255));
        $blockTable->addColumn('view_type', 'string', array('length' => 255));
        $blockTable->addColumn('name', 'string', array('length' => 255));
        $blockTable->addColumn('parameters', 'text', array('length' => 65535));

        $blockTable->setPrimaryKey(array('id', 'status'));
        $blockTable->addForeignKeyConstraint('ngbm_layout', array('layout_id', 'status'), array('id', 'status'));

        // ngbm_rule table

        $ruleTable = $schema->createTable('ngbm_rule');

        $ruleTable->addColumn('id', 'integer', array('autoincrement' => true));
        $ruleTable->addColumn('layout_id', 'integer');
        $ruleTable->addColumn('target_identifier', 'string', array('length' => 255));

        $ruleTable->setPrimaryKey(array('id'));

        // ngbm_rule_value table

        $ruleValueTable = $schema->createTable('ngbm_rule_value');

        $ruleValueTable->addColumn('id', 'integer', array('autoincrement' => true));
        $ruleValueTable->addColumn('rule_id', 'integer');
        $ruleValueTable->addColumn('value', 'text', array('length' => 65535));

        $ruleValueTable->setPrimaryKey(array('id'));
        $ruleValueTable->addForeignKeyConstraint('ngbm_rule', array('rule_id'), array('id'));

        // ngbm_rule_condition table

        $ruleConditionTable = $schema->createTable('ngbm_rule_condition');

        $ruleConditionTable->addColumn('id', 'integer', array('autoincrement' => true));
        $ruleConditionTable->addColumn('rule_id', 'integer');
        $ruleConditionTable->addColumn('identifier', 'string', array('length' => 255));
        $ruleConditionTable->addColumn('parameters', 'text', array('length' => 65535));

        $ruleConditionTable->setPrimaryKey(array('id'));
        $ruleConditionTable->addForeignKeyConstraint('ngbm_rule', array('rule_id'), array('id'));
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('ngbm_block');
        $schema->dropTable('ngbm_zone');
        $schema->dropTable('ngbm_layout');

        $schema->dropTable('ngbm_rule_value');
        $schema->dropTable('ngbm_rule_condition');
        $schema->dropTable('ngbm_rule');
    }
}
