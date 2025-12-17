<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version001100 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        $this->addSql('CREATE INDEX idx_ngl_layout_type ON ngbm_layout (type)');
        $this->addSql('CREATE INDEX idx_ngl_layout_shared ON ngbm_layout (shared)');

        $this->addSql('CREATE INDEX idx_ngl_linked_zone ON ngbm_zone (linked_layout_id, linked_zone_identifier)');

        $this->addSql('ALTER TABLE ngbm_collection_item ADD COLUMN config text NOT NULL');
        $this->addSql('ALTER TABLE ngbm_collection_item CHANGE value_id value varchar(191)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        $this->addSql('DROP INDEX idx_ngl_layout_type ON ngbm_layout');
        $this->addSql('DROP INDEX idx_ngl_layout_shared ON ngbm_layout');

        $this->addSql('DROP INDEX idx_ngl_linked_zone ON ngbm_zone');

        $this->addSql('ALTER TABLE ngbm_collection_item DROP COLUMN config');
        $this->addSql('ALTER TABLE ngbm_collection_item CHANGE value value_id varchar(191)');
    }
}
