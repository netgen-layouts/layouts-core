<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version000700 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        // ngbm_layout table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_layout (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              type varchar(191) NOT NULL,
              name varchar(191) NOT NULL,
              created int NOT NULL,
              modified int NOT NULL,
              shared tinyint NOT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_layout_name (name)
            )
            EOT
        );

        // ngbm_block table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_block (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              layout_id int NOT NULL,
              depth int NOT NULL,
              path varchar(191) NOT NULL,
              parent_id int DEFAULT NULL,
              placeholder varchar(191) DEFAULT NULL,
              position int DEFAULT NULL,
              definition_identifier varchar(191) NOT NULL,
              view_type varchar(191) NOT NULL,
              item_view_type varchar(191) NOT NULL,
              name varchar(191) NOT NULL,
              placeholder_parameters longtext NOT NULL,
              parameters longtext NOT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_layout (layout_id,status),
              KEY idx_ngl_parent_block (parent_id,placeholder,status),
              CONSTRAINT fk_ngl_block_layout FOREIGN KEY (layout_id, status)
                REFERENCES ngbm_layout (id, status)
            )
            EOT
        );

        // ngbm_zone table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_zone (
              identifier varchar(191) NOT NULL,
              layout_id int NOT NULL,
              status int NOT NULL,
              root_block_id int NOT NULL,
              linked_layout_id int DEFAULT NULL,
              linked_zone_identifier varchar(191) DEFAULT NULL,
              PRIMARY KEY (identifier,layout_id,status),
              KEY idx_ngl_layout (layout_id,status),
              KEY idx_ngl_root_block (root_block_id,status),
              CONSTRAINT fk_ngl_zone_block FOREIGN KEY (root_block_id, status)
                REFERENCES ngbm_block (id, status),
              CONSTRAINT fk_ngl_zone_layout FOREIGN KEY (layout_id, status)
                REFERENCES ngbm_layout (id, status)
            )
            EOT
        );

        // ngbm_rule table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_rule (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              layout_id int DEFAULT NULL,
              comment varchar(191) DEFAULT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_related_layout (layout_id)
            )
            EOT
        );

        // ngbm_rule_data table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_rule_data (
              rule_id int NOT NULL,
              enabled tinyint NOT NULL,
              priority int NOT NULL,
              PRIMARY KEY (rule_id)
            )
            EOT
        );

        // ngbm_rule_target table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_rule_target (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              rule_id int NOT NULL,
              type varchar(191) NOT NULL,
              value longtext DEFAULT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_rule (rule_id,status),
              KEY idx_ngl_target_type (type),
              CONSTRAINT fk_ngl_target_rule FOREIGN KEY (rule_id, status)
                REFERENCES ngbm_rule (id, status)
            )
            EOT
        );

        // ngbm_rule_condition table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_rule_condition (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              rule_id int NOT NULL,
              type varchar(191) NOT NULL,
              value longtext DEFAULT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_rule (rule_id,status),
              CONSTRAINT fk_ngl_condition_rule FOREIGN KEY (rule_id, status)
                REFERENCES ngbm_rule (id, status)
            )
            EOT
        );

        // ngbm_collection table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_collection (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              type int NOT NULL,
              shared tinyint NOT NULL,
              name varchar(191) DEFAULT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_collection_name (name)
            )
            EOT
        );

        // ngbm_collection_item table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_collection_item (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              collection_id int NOT NULL,
              position int NOT NULL,
              type int NOT NULL,
              value_id varchar(191) NOT NULL,
              value_type varchar(191) NOT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_collection (collection_id,status),
              CONSTRAINT fk_ngl_item_collection FOREIGN KEY (collection_id, status)
                REFERENCES ngbm_collection (id, status)
            )
            EOT
        );

        // ngbm_collection_query table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_collection_query (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              collection_id int NOT NULL,
              position int NOT NULL,
              identifier varchar(191) NOT NULL,
              type varchar(191) NOT NULL,
              parameters longtext NOT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_collection (collection_id,status),
              CONSTRAINT fk_ngl_query_collection FOREIGN KEY (collection_id, status)
                REFERENCES ngbm_collection (id, status)
            )
            EOT
        );

        // ngbm_block_collection table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_block_collection (
              block_id int NOT NULL,
              block_status int NOT NULL,
              collection_id int NOT NULL,
              collection_status int NOT NULL,
              identifier varchar(191) NOT NULL,
              start int NOT NULL,
              length int DEFAULT NULL,
              PRIMARY KEY (block_id,block_status,identifier),
              KEY idx_ngl_block (block_id,block_status),
              KEY idx_ngl_collection (collection_id,collection_status),
              CONSTRAINT fk_ngl_block_collection_block FOREIGN KEY (block_id, block_status)
                REFERENCES ngbm_block (id, status),
              CONSTRAINT fk_ngl_block_collection_collection FOREIGN KEY (collection_id, collection_status)
                REFERENCES ngbm_collection (id, status)
            )
            EOT
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        $this->addSql('DROP TABLE IF EXISTS ngbm_block_collection');
        $this->addSql('DROP TABLE IF EXISTS ngbm_collection_item');
        $this->addSql('DROP TABLE IF EXISTS ngbm_collection_query');
        $this->addSql('DROP TABLE IF EXISTS ngbm_collection');

        $this->addSql('DROP TABLE IF EXISTS ngbm_zone');
        $this->addSql('DROP TABLE IF EXISTS ngbm_block');
        $this->addSql('DROP TABLE IF EXISTS ngbm_layout');

        $this->addSql('DROP TABLE IF EXISTS ngbm_rule_target');
        $this->addSql('DROP TABLE IF EXISTS ngbm_rule_condition');
        $this->addSql('DROP TABLE IF EXISTS ngbm_rule_data');
        $this->addSql('DROP TABLE IF EXISTS ngbm_rule');
    }
}
