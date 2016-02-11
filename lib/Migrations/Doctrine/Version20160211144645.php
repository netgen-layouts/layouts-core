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
        $layoutTable->addColumn('identifier', 'string', array('length' => 255));
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

        // ngbm_rule table

        $blockTable = $schema->createTable('ngbm_rule');

        $blockTable->addColumn('id', 'integer', array('autoincrement' => true));
        $blockTable->addColumn('layout_id', 'integer');
        $blockTable->addColumn('target_identifier', 'string', array('length' => 255));

        $blockTable->setPrimaryKey(array('id'));

        // ngbm_rule_value table

        $blockTable = $schema->createTable('ngbm_rule_value');

        $blockTable->addColumn('id', 'integer', array('autoincrement' => true));
        $blockTable->addColumn('rule_id', 'integer');
        $blockTable->addColumn('value', 'text', array('length' => 65535));

        $blockTable->setPrimaryKey(array('id'));

        // ngbm_rule_condition table

        $blockTable = $schema->createTable('ngbm_rule_condition');

        $blockTable->addColumn('id', 'integer', array('autoincrement' => true));
        $blockTable->addColumn('rule_id', 'integer');
        $blockTable->addColumn('identifier', 'string', array('length' => 255));
        $blockTable->addColumn('parameters', 'text', array('length' => 65535));

        $blockTable->setPrimaryKey(array('id'));
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('ngbm_layout');
        $schema->dropTable('ngbm_zone');
        $schema->dropTable('ngbm_block');

        $schema->dropTable('ngbm_rule');
        $schema->dropTable('ngbm_rule_value');
        $schema->dropTable('ngbm_rule_condition');
    }
}
