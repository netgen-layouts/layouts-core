<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version000800 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN config longtext NOT NULL');
        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN placeholder_parameters');

        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN type');
        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN shared');
        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN name');

        $this->addSql('ALTER TABLE ngbm_collection_query DROP COLUMN position');
        $this->addSql('ALTER TABLE ngbm_collection_query DROP COLUMN identifier');

        $this->addSql('ALTER TABLE ngbm_layout ADD COLUMN description text NOT NULL AFTER name');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN placeholder_parameters longtext NOT NULL');
        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN config');

        $this->addSql('ALTER TABLE ngbm_layout DROP COLUMN description');

        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN type int NOT NULL');
        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN shared tinyint NOT NULL');
        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN name varchar(191) DEFAULT NULL');

        $this->addSql('CREATE INDEX idx_ngl_collection_name ON ngbm_collection (name)');

        $this->addSql('ALTER TABLE ngbm_collection_query ADD COLUMN position int(11) NOT NULL AFTER collection_id');
        $this->addSql('ALTER TABLE ngbm_collection_query ADD COLUMN identifier varchar(191) NOT NULL AFTER position');
    }
}
