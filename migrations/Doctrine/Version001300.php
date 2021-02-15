<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

final class Version001300 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        // ngbm_role table

        $roleTable = $schema->createTable('ngbm_role');

        $roleTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $roleTable->addColumn('status', 'integer');
        $roleTable->addColumn('name', 'string', ['length' => 191]);
        $roleTable->addColumn('identifier', 'string', ['length' => 191]);
        $roleTable->addColumn('description', 'text', ['length' => 65535]);

        $roleTable->setPrimaryKey(['id', 'status']);

        $roleTable->addIndex(['identifier'], 'idx_ngl_role_identifier');

        // ngbm_role_policy table

        $rolePolicyTable = $schema->createTable('ngbm_role_policy');

        $rolePolicyTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $rolePolicyTable->addColumn('status', 'integer');
        $rolePolicyTable->addColumn('role_id', 'integer');
        $rolePolicyTable->addColumn('component', 'string', ['length' => 191, 'notnull' => false]);
        $rolePolicyTable->addColumn('permission', 'string', ['length' => 191, 'notnull' => false]);
        $rolePolicyTable->addColumn('limitations', 'text', ['length' => 65535]);

        $rolePolicyTable->setPrimaryKey(['id', 'status']);
        $rolePolicyTable->addForeignKeyConstraint('ngbm_role', ['role_id', 'status'], ['id', 'status'], [], 'fk_ngl_policy_role');

        $rolePolicyTable->addIndex(['role_id', 'status'], 'idx_ngl_role');
        $rolePolicyTable->addIndex(['component'], 'idx_ngl_policy_component');
        $rolePolicyTable->addIndex(['component', 'permission'], 'idx_ngl_policy_component_permission');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $schema->dropTable('ngbm_role_policy');
        $schema->dropTable('ngbm_role');
    }
}
