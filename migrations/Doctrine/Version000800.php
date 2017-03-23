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
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $blockTable = $schema->getTable('ngbm_block');
        $blockTable->dropColumn('config');
    }
}
