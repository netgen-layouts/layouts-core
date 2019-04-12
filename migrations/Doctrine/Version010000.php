<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version010000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('RENAME TABLE ngbm_block TO nglayouts_block');
        $this->addSql('RENAME TABLE ngbm_block_collection TO nglayouts_block_collection');
        $this->addSql('RENAME TABLE ngbm_block_translation TO nglayouts_block_translation');
        $this->addSql('RENAME TABLE ngbm_collection TO nglayouts_collection');
        $this->addSql('RENAME TABLE ngbm_collection_item TO nglayouts_collection_item');
        $this->addSql('RENAME TABLE ngbm_collection_query TO nglayouts_collection_query');
        $this->addSql('RENAME TABLE ngbm_collection_query_translation TO nglayouts_collection_query_translation');
        $this->addSql('RENAME TABLE ngbm_collection_translation TO nglayouts_collection_translation');
        $this->addSql('RENAME TABLE ngbm_layout TO nglayouts_layout');
        $this->addSql('RENAME TABLE ngbm_layout_translation TO nglayouts_layout_translation');
        $this->addSql('RENAME TABLE ngbm_role TO nglayouts_role');
        $this->addSql('RENAME TABLE ngbm_role_policy TO nglayouts_role_policy');
        $this->addSql('RENAME TABLE ngbm_rule TO nglayouts_rule');
        $this->addSql('RENAME TABLE ngbm_rule_condition TO nglayouts_rule_condition');
        $this->addSql('RENAME TABLE ngbm_rule_data TO nglayouts_rule_data');
        $this->addSql('RENAME TABLE ngbm_rule_target TO nglayouts_rule_target');
        $this->addSql('RENAME TABLE ngbm_zone TO nglayouts_zone');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('RENAME TABLE nglayouts_block TO ngbm_block');
        $this->addSql('RENAME TABLE nglayouts_block_collection TO ngbm_block_collection');
        $this->addSql('RENAME TABLE nglayouts_block_translation TO ngbm_block_translation');
        $this->addSql('RENAME TABLE nglayouts_collection TO ngbm_collection');
        $this->addSql('RENAME TABLE nglayouts_collection_item TO ngbm_collection_item');
        $this->addSql('RENAME TABLE nglayouts_collection_query TO ngbm_collection_query');
        $this->addSql('RENAME TABLE nglayouts_collection_query_translation TO ngbm_collection_query_translation');
        $this->addSql('RENAME TABLE nglayouts_collection_translation TO ngbm_collection_translation');
        $this->addSql('RENAME TABLE nglayouts_layout TO ngbm_layout');
        $this->addSql('RENAME TABLE nglayouts_layout_translation TO ngbm_layout_translation');
        $this->addSql('RENAME TABLE nglayouts_role TO ngbm_role');
        $this->addSql('RENAME TABLE nglayouts_role_policy TO ngbm_role_policy');
        $this->addSql('RENAME TABLE nglayouts_rule TO ngbm_rule');
        $this->addSql('RENAME TABLE nglayouts_rule_condition TO ngbm_rule_condition');
        $this->addSql('RENAME TABLE nglayouts_rule_data TO ngbm_rule_data');
        $this->addSql('RENAME TABLE nglayouts_rule_target TO ngbm_rule_target');
        $this->addSql('RENAME TABLE nglayouts_zone TO ngbm_zone');
    }
}
