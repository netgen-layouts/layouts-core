<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

final class Version001100 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $layoutTable = $schema->getTable('ngbm_layout');
        $layoutTable->addIndex(['type'], 'idx_ngl_layout_type');
        $layoutTable->addIndex(['shared'], 'idx_ngl_layout_shared');

        $zoneTable = $schema->getTable('ngbm_zone');
        $zoneTable->addIndex(['linked_layout_id', 'linked_zone_identifier'], 'idx_ngl_linked_zone');

        $this->addSql('ALTER TABLE ngbm_collection_item ADD COLUMN config text NOT NULL');
        $this->addSql('ALTER TABLE ngbm_collection_item CHANGE value_id value varchar(191)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('DROP INDEX idx_ngl_layout_type ON ngbm_layout');
        $this->addSql('DROP INDEX idx_ngl_layout_shared ON ngbm_layout');

        $this->addSql('DROP INDEX idx_ngl_linked_zone ON ngbm_zone');

        $this->addSql('ALTER TABLE ngbm_collection_item DROP COLUMN config');
        $this->addSql('ALTER TABLE ngbm_collection_item CHANGE value value_id varchar(191)');
    }
}
