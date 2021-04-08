<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

final class Version010300 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql(
            <<<'EOT'
            CREATE TABLE nglayouts_rule_group (
              id int(11) NOT NULL AUTO_INCREMENT,
              status int(11) NOT NULL,
              uuid char(36) NOT NULL,
              depth int(11) NOT NULL,
              path varchar(191) NOT NULL,
              parent_id int(11) DEFAULT NULL,
              name varchar(191) NOT NULL,
              description longtext NOT NULL,
              PRIMARY KEY (id, status),
              UNIQUE KEY idx_ngl_rule_group_uuid (uuid, status),
              KEY idx_ngl_parent_rule_group (parent_id)
            )
            EOT
        );

        $this->addSql(
            <<<'EOT'
            CREATE TABLE nglayouts_rule_group_data (
              rule_group_id int(11) NOT NULL,
              enabled tinyint(1) NOT NULL,
              priority int(11) NOT NULL,
              PRIMARY KEY (rule_group_id)
            )
            EOT
        );

        $this->addSql('ALTER TABLE nglayouts_rule ADD COLUMN rule_group_id int(11) NOT NULL AFTER uuid');
        $this->addSql('ALTER TABLE nglayouts_rule CHANGE comment description longtext NOT NULL');

        $this->addSql(
            <<<'EOT'
            INSERT INTO nglayouts_rule_group (
              id, status, uuid, depth, path, parent_id, name, description
            ) VALUES (
              1, 1, '00000000-0000-0000-0000-000000000000', 0, '/1/', NULL, 'Root', ''
            )
            EOT
        );

        $this->addSql(
            <<<'EOT'
            INSERT INTO nglayouts_rule_group_data (
              rule_group_id, enabled, priority
            ) VALUES (
              1, 1, 0
            )
            EOT
        );

        $this->addSql('UPDATE nglayouts_rule SET rule_group_id = 1');

        $this->addSql(
            <<<'EOT'
            CREATE TABLE nglayouts_rule_condition_rule (
              condition_id int NOT NULL,
              condition_status int NOT NULL,
              rule_id int NOT NULL,
              rule_status int NOT NULL,
              PRIMARY KEY (condition_id, condition_status),
              KEY idx_ngl_rule (rule_id, rule_status),
              CONSTRAINT fk_ngl_rule_condition_rule_rule_condition FOREIGN KEY (condition_id, condition_status) REFERENCES nglayouts_rule_condition (id, status),
              CONSTRAINT fk_ngl_rule_condition_rule_rule FOREIGN KEY (rule_id, rule_status) REFERENCES nglayouts_rule (id, status)
            )
            EOT
        );

        $this->addSql(
            <<<'EOT'
            CREATE TABLE nglayouts_rule_condition_rule_group (
              condition_id int NOT NULL,
              condition_status int NOT NULL,
              rule_group_id int NOT NULL,
              rule_group_status int NOT NULL,
              PRIMARY KEY (condition_id, condition_status),
              KEY idx_ngl_rule_group (rule_group_id, rule_group_status),
              CONSTRAINT fk_ngl_rule_condition_rule_group_rule_condition FOREIGN KEY (condition_id, condition_status) REFERENCES nglayouts_rule_condition (id, status),
              CONSTRAINT fk_ngl_rule_condition_rule_group_rule_group FOREIGN KEY (rule_group_id, rule_group_status) REFERENCES nglayouts_rule_group (id, status)
            )
            EOT
        );

        $this->addSql(
            <<<'EOT'
            INSERT INTO nglayouts_rule_condition_rule (
              condition_id,
              condition_status,
              rule_id,
              rule_status
            ) SELECT id, status, rule_id, status FROM nglayouts_rule_condition
            EOT
        );

        $this->addSql('ALTER TABLE nglayouts_rule_condition DROP FOREIGN KEY fk_ngl_condition_rule');
        $this->addSql('ALTER TABLE nglayouts_rule_condition DROP KEY idx_ngl_rule');
        $this->addSql('ALTER TABLE nglayouts_rule_condition DROP COLUMN rule_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('DROP TABLE nglayouts_rule_condition_rule_group');

        $this->addSql(
            <<<'EOT'
            DELETE nglayouts_rule_condition.* FROM nglayouts_rule_condition
            LEFT JOIN nglayouts_rule_condition_rule
                ON nglayouts_rule_condition.id = nglayouts_rule_condition_rule.condition_id
                AND nglayouts_rule_condition.status = nglayouts_rule_condition_rule.condition_status
            WHERE nglayouts_rule_condition_rule.rule_id IS NULL
            EOT
        );

        $this->addSql('ALTER TABLE nglayouts_rule_condition ADD COLUMN rule_id int(11) NOT NULL AFTER uuid');

        $this->addSql(
            <<<'EOT'
            UPDATE nglayouts_rule_condition
            INNER JOIN nglayouts_rule_condition_rule
                ON nglayouts_rule_condition.id = nglayouts_rule_condition_rule.condition_id
                AND nglayouts_rule_condition.status = nglayouts_rule_condition_rule.condition_status
            SET nglayouts_rule_condition.rule_id = nglayouts_rule_condition_rule.rule_id
            EOT
        );

        $this->addSql('DROP TABLE nglayouts_rule_condition_rule');

        $this->addSql('ALTER TABLE nglayouts_rule_condition ADD CONSTRAINT fk_ngl_condition_rule FOREIGN KEY (rule_id, status) REFERENCES nglayouts_rule (id, status)');
        $this->addSql('ALTER TABLE nglayouts_rule_condition ADD KEY idx_ngl_rule (rule_id, status)');

        $this->addSql('ALTER TABLE nglayouts_rule DROP COLUMN rule_group_id');
        $this->addSql('ALTER TABLE nglayouts_rule CHANGE description comment longtext NOT NULL');

        $this->addSql('DROP TABLE nglayouts_rule_group_data');
        $this->addSql('DROP TABLE nglayouts_rule_group');
    }
}
