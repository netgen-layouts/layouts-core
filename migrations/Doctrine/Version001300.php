<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version001300 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        // ngbm_role table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_role (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              name varchar(191) NOT NULL,
              identifier varchar(191) NOT NULL,
              description longtext NOT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_role_identifier (identifier)
            )
            EOT
        );

        // ngbm_role_policy table

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_role_policy (
              id int NOT NULL AUTO_INCREMENT,
              status int NOT NULL,
              role_id int NOT NULL,
              component varchar(191) DEFAULT NULL,
              permission varchar(191) DEFAULT NULL,
              limitations longtext NOT NULL,
              PRIMARY KEY (id,status),
              KEY idx_ngl_role (role_id,status),
              KEY idx_ngl_policy_component (component),
              KEY idx_ngl_policy_component_permission (component,permission),
              CONSTRAINT fk_ngl_policy_role FOREIGN KEY (role_id, status)
                REFERENCES ngbm_role (id, status)
            )
            EOT
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on MySQL.');

        $this->addSql('DROP TABLE IF EXISTS ngbm_role_policy');
        $this->addSql('DROP TABLE IF EXISTS ngbm_role');
    }
}
