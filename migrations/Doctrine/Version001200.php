<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

final class Version001200 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_collection_item DROP COLUMN type');

        $this->addSql('UPDATE ngbm_rule SET comment = "" WHERE comment IS NULL');
        $this->addSql('ALTER TABLE ngbm_rule MODIFY COLUMN comment text NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_collection_item ADD COLUMN type int(11) NOT NULL AFTER position');
        $this->addSql('UPDATE ngbm_collection_item SET type = 0');

        $this->addSql('ALTER TABLE ngbm_rule MODIFY COLUMN comment varchar(191) DEFAULT NULL');
    }
}
