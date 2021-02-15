<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

final class Version001000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN start int(11) NOT NULL AFTER status');
        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN length int(11) AFTER start');

        $this->addSql(
            <<<'EOT'
            UPDATE ngbm_collection AS c
            INNER JOIN ngbm_block_collection AS bc
                ON c.id = bc.collection_id
                AND c.status = bc.collection_status
            SET c.start = bc.start, c.length = bc.length
            EOT
        );

        $this->addSql('ALTER TABLE ngbm_block_collection DROP COLUMN start');
        $this->addSql('ALTER TABLE ngbm_block_collection DROP COLUMN length');

        $this->addSql('ALTER TABLE ngbm_collection_item MODIFY COLUMN value_id varchar(191)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_block_collection ADD COLUMN start int(11) NOT NULL AFTER collection_status');
        $this->addSql('ALTER TABLE ngbm_block_collection ADD COLUMN length int(11) AFTER start');

        $this->addSql(
            <<<'EOT'
            UPDATE ngbm_block_collection AS bc
            INNER JOIN ngbm_collection AS c
                ON bc.collection_id = c.id
                AND bc.collection_status = c.status
            SET bc.start = c.start, bc.length = c.length
            EOT
        );

        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN start');
        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN length');

        $this->addSql('UPDATE ngbm_collection_item SET value_id = "(INVALID)" WHERE value_id IS NULL');
        $this->addSql('ALTER TABLE ngbm_collection_item MODIFY COLUMN value_id varchar(191) NOT NULL');
    }
}
