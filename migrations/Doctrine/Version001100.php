<?php

namespace Netgen\BlockManager\Migrations\Doctrine;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version001100 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $itemTable = $schema->getTable('ngbm_collection_item');
        $itemTable->addColumn('config', 'text', array('length' => 65535));
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $itemTable = $schema->getTable('ngbm_collection_item');
        $itemTable->dropColumn('config');
    }
}
