<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;

final class Version000800 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $blockTable = $schema->getTable('ngbm_block');
        $blockTable->addColumn('config', 'text', ['length' => 65535]);
        $blockTable->dropColumn('placeholder_parameters');

        $collectionTable = $schema->getTable('ngbm_collection');
        $collectionTable->dropColumn('type');
        $collectionTable->dropColumn('shared');
        $collectionTable->dropColumn('name');

        $collectionQueryTable = $schema->getTable('ngbm_collection_query');
        $collectionQueryTable->dropColumn('position');
        $collectionQueryTable->dropColumn('identifier');

        $this->addSql('ALTER TABLE ngbm_layout ADD COLUMN description text NOT NULL AFTER name');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $blockTable = $schema->getTable('ngbm_block');
        $blockTable->addColumn('placeholder_parameters', 'text', ['length' => 65535]);
        $blockTable->dropColumn('config');

        $layoutTable = $schema->getTable('ngbm_layout');
        $layoutTable->dropColumn('description');

        $collectionTable = $schema->getTable('ngbm_collection');
        $collectionTable->addColumn('type', 'integer');
        $collectionTable->addColumn('shared', 'boolean');
        $collectionTable->addColumn('name', 'string', ['length' => 191, 'notnull' => false]);

        $collectionTable->addIndex(['name'], 'idx_ngl_collection_name');

        $this->addSql('ALTER TABLE ngbm_collection_query ADD COLUMN position int(11) NOT NULL AFTER collection_id');
        $this->addSql('ALTER TABLE ngbm_collection_query ADD COLUMN identifier varchar(191) NOT NULL AFTER position');
    }
}
