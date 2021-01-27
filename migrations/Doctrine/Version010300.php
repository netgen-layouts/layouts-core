<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version010300 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql(
            <<<'EOT'
            CREATE TABLE `nglayouts_rule_group` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `status` int(11) NOT NULL,
              `uuid` char(36) NOT NULL,
              `depth` int(11) NOT NULL,
              `path` varchar(191) NOT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `comment` longtext NOT NULL,
              PRIMARY KEY (`id`,`status`),
              UNIQUE KEY `idx_ngl_rule_group_uuid` (`uuid`, `status`),
              KEY `idx_ngl_parent_rule_group` (`parent_id`)
            )
            EOT
        );

        $this->addSql(
            <<<'EOT'
            CREATE TABLE `nglayouts_rule_group_data` (
              `rule_group_id` int(11) NOT NULL,
              `enabled` tinyint(1) NOT NULL,
              `priority` int(11) NOT NULL,
              PRIMARY KEY (`rule_group_id`)
            )
            EOT
        );

        $this->addSql('ALTER TABLE nglayouts_rule ADD COLUMN rule_group_id int(11) NOT NULL AFTER uuid');

        $this->addSql(
            <<<'EOT'
            INSERT INTO nglayouts_rule_group (
                `id`, `status`, `uuid`, `depth`, `path`, `parent_id`, `comment`
            ) VALUES (
                1, 1, '00000000-0000-0000-0000-000000000000', 0, '/1/', NULL, ''
            )
            EOT
        );

        $this->addSql(
            <<<'EOT'
            INSERT INTO nglayouts_rule_group_data (
                `rule_group_id`, `enabled`, `priority`
            ) VALUES (
                1, 1, 0
            )
            EOT
        );

        $this->addSql('UPDATE nglayouts_rule SET rule_group_id = 1');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE nglayouts_rule DROP COLUMN rule_group_id');
        $this->addSql('DROP TABLE nglayouts_rule_group_data');
        $this->addSql('DROP TABLE nglayouts_rule_group');
    }
}
