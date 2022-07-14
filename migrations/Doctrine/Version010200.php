<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

use function sprintf;

final class Version010200 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        // Make sure we only run the migration if the UUID migration script from Netgen Layouts 1.0 has been ran
        $queryResult = $this->connection->executeQuery('SELECT COUNT(*) as count FROM nglayouts_layout WHERE LENGTH(uuid) < 36');

        $this->abortIf(
            ((int) $queryResult->fetchAllAssociative()[0]['count']) > 0,
            sprintf(
                '%s %s',
                'Database migration to version 1.2 can only be executed safely after you have ran the UUID migration script from 1.0 upgrade.',
                'Run the UUID migration script and then run Doctrine Migrations again.',
            ),
        );

        $this->addSql('ALTER TABLE nglayouts_block CHANGE config config LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_block_translation CHANGE parameters parameters LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_collection_item CHANGE config config LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_collection_query_translation CHANGE parameters parameters LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_layout CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_role CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_role_policy CHANGE limitations limitations LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule CHANGE comment comment LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE nglayouts_rule_condition CHANGE value value LONGTEXT');
        $this->addSql('ALTER TABLE nglayouts_rule_target CHANGE value value LONGTEXT');

        $this->addSql('ALTER TABLE nglayouts_rule ADD COLUMN layout_uuid char(36) DEFAULT NULL AFTER layout_id');
        $this->addSql('ALTER TABLE nglayouts_rule DROP KEY idx_ngl_related_layout, ADD KEY idx_ngl_related_layout(layout_uuid)');
        $this->addSql('UPDATE nglayouts_rule r LEFT JOIN nglayouts_layout l ON r.layout_id = l.id SET r.layout_uuid = l.uuid');
        $this->addSql('ALTER TABLE nglayouts_rule DROP COLUMN layout_id');

        $this->addSql('ALTER TABLE nglayouts_zone ADD COLUMN linked_layout_uuid char(36) DEFAULT NULL AFTER linked_layout_id');
        $this->addSql('ALTER TABLE nglayouts_zone DROP KEY idx_ngl_linked_zone, ADD KEY idx_ngl_linked_zone(linked_layout_uuid, linked_zone_identifier)');
        $this->addSql('UPDATE nglayouts_zone z LEFT JOIN nglayouts_layout l ON z.linked_layout_id = l.id SET z.linked_layout_uuid = l.uuid');
        $this->addSql('ALTER TABLE nglayouts_zone DROP COLUMN linked_layout_id');
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
        $this->addSql('ALTER TABLE nglayouts_rule_condition CHANGE value value TEXT');
        $this->addSql('ALTER TABLE nglayouts_rule_target CHANGE value value TEXT');

        $this->addSql('ALTER TABLE nglayouts_rule ADD COLUMN layout_id int(11) DEFAULT NULL AFTER layout_uuid');
        $this->addSql('ALTER TABLE nglayouts_rule DROP KEY idx_ngl_related_layout, ADD KEY idx_ngl_related_layout(layout_id)');
        $this->addSql('UPDATE nglayouts_rule r LEFT JOIN nglayouts_layout l ON r.layout_uuid = l.uuid SET r.layout_id = l.id');
        $this->addSql('ALTER TABLE nglayouts_rule DROP COLUMN layout_uuid');

        $this->addSql('ALTER TABLE nglayouts_zone ADD COLUMN linked_layout_id int(11) DEFAULT NULL AFTER linked_layout_uuid');
        $this->addSql('ALTER TABLE nglayouts_zone DROP KEY idx_ngl_linked_zone, ADD KEY idx_ngl_linked_zone(linked_layout_id, linked_zone_identifier)');
        $this->addSql('UPDATE nglayouts_zone z LEFT JOIN nglayouts_layout l ON z.linked_layout_uuid = l.uuid SET z.linked_layout_id = l.id');
        $this->addSql('ALTER TABLE nglayouts_zone DROP COLUMN linked_layout_uuid');
    }
}
