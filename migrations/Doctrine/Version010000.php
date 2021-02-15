<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

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

        $this->addSql('ALTER TABLE nglayouts_layout ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_layout SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_layout ADD UNIQUE INDEX idx_ngl_layout_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_block ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_block SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_block ADD UNIQUE INDEX idx_ngl_block_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_rule ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_rule SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_rule ADD UNIQUE INDEX idx_ngl_rule_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_rule_target ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_rule_target SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_rule_target ADD UNIQUE INDEX idx_ngl_rule_target_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_rule_condition ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_rule_condition SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_rule_condition ADD UNIQUE INDEX idx_ngl_rule_condition_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_collection ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_collection SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_collection ADD UNIQUE INDEX idx_ngl_collection_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_collection_item ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_collection_item SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_collection_item ADD UNIQUE INDEX idx_ngl_collection_item_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_collection_query ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_collection_query SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_collection_query ADD UNIQUE INDEX idx_ngl_collection_query_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_role ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_role SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_role ADD UNIQUE INDEX idx_ngl_role_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_role_policy ADD COLUMN uuid char(36) NOT NULL AFTER status');
        $this->addSql('UPDATE nglayouts_role_policy SET uuid = id');
        $this->addSql('ALTER TABLE nglayouts_role_policy ADD UNIQUE INDEX idx_ngl_role_policy_uuid (uuid, status)');

        $this->addSql('ALTER TABLE nglayouts_collection_item ADD COLUMN view_type varchar(191) DEFAULT NULL AFTER value_type');

        $this->addSql(
            <<<'EOT'
            CREATE TABLE nglayouts_collection_slot (
              id int(11) NOT NULL AUTO_INCREMENT,
              status int(11) NOT NULL,
              uuid char(36) NOT NULL,
              collection_id int(11) NOT NULL,
              position int(11) NOT NULL,
              view_type varchar(191) DEFAULT NULL,
              PRIMARY KEY (id,status),
              UNIQUE KEY idx_ngl_collection_slot_uuid (uuid, status),
              KEY idx_ngl_collection (collection_id,status),
              KEY idx_ngl_position (collection_id,position),
              CONSTRAINT fk_ngl_slot_collection FOREIGN KEY (collection_id, status)
                REFERENCES nglayouts_collection (id, status)
            )
            EOT
        );

        $this->addSql('UPDATE nglayouts_rule_target SET type = "ez_content" WHERE type = "ezcontent"');
        $this->addSql('UPDATE nglayouts_rule_target SET type = "ez_location" WHERE type = "ezlocation"');
        $this->addSql('UPDATE nglayouts_rule_target SET type = "ez_subtree" WHERE type = "ezsubtree"');
        $this->addSql('UPDATE nglayouts_rule_target SET type = "ez_children" WHERE type = "ezchildren"');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('UPDATE nglayouts_rule_target SET type = "ezcontent" WHERE type = "ez_content"');
        $this->addSql('UPDATE nglayouts_rule_target SET type = "ezlocation" WHERE type = "ez_location"');
        $this->addSql('UPDATE nglayouts_rule_target SET type = "ezsubtree" WHERE type = "ez_subtree"');
        $this->addSql('UPDATE nglayouts_rule_target SET type = "ezchildren" WHERE type = "ez_children"');

        $this->addSql('DROP TABLE nglayouts_collection_slot');

        $this->addSql('ALTER TABLE nglayouts_collection_item DROP COLUMN view_type');

        $this->addSql('ALTER TABLE nglayouts_role_policy DROP INDEX idx_ngl_role_policy_uuid');
        $this->addSql('ALTER TABLE nglayouts_role_policy DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_role DROP INDEX idx_ngl_role_uuid');
        $this->addSql('ALTER TABLE nglayouts_role DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_collection_query DROP INDEX idx_ngl_collection_query_uuid');
        $this->addSql('ALTER TABLE nglayouts_collection_query DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_collection_item DROP INDEX idx_ngl_collection_item_uuid');
        $this->addSql('ALTER TABLE nglayouts_collection_item DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_collection DROP INDEX idx_ngl_collection_uuid');
        $this->addSql('ALTER TABLE nglayouts_collection DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_rule_condition DROP INDEX idx_ngl_rule_condition_uuid');
        $this->addSql('ALTER TABLE nglayouts_rule_condition DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_rule_target DROP INDEX idx_ngl_rule_target_uuid');
        $this->addSql('ALTER TABLE nglayouts_rule_target DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_rule DROP INDEX idx_ngl_rule_uuid');
        $this->addSql('ALTER TABLE nglayouts_rule DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_block DROP INDEX idx_ngl_block_uuid');
        $this->addSql('ALTER TABLE nglayouts_block DROP COLUMN uuid');

        $this->addSql('ALTER TABLE nglayouts_layout DROP INDEX idx_ngl_layout_uuid');
        $this->addSql('ALTER TABLE nglayouts_layout DROP COLUMN uuid');

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
