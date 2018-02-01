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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_collection_item ADD COLUMN config text NOT NULL');
        $this->addSql('ALTER TABLE ngbm_collection_item CHANGE value_id value varchar(255)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_collection_item DROP COLUMN config');
        $this->addSql('ALTER TABLE ngbm_collection_item CHANGE value value_id varchar(255)');
    }
}
