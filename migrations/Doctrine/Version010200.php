<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version010200 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE nglayouts_block CHANGE config config LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_block_translation CHANGE parameters parameters LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_collection_item CHANGE config config LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_collection_query_translation CHANGE parameters parameters LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_layout CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_role CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_role_policy CHANGE limitations limitations LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule CHANGE comment comment LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule_condition CHANGE value value LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule_target CHANGE value value LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE nglayouts_block CHANGE config config TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_block_translation CHANGE parameters parameters TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_collection_item CHANGE config config TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_collection_query_translation CHANGE parameters parameters TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_layout CHANGE description description TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_role CHANGE description description TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_role_policy CHANGE limitations limitations TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule CHANGE comment comment TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule_condition CHANGE value value TEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule_target CHANGE value value TEXT NOT NULL');
    }
}
