<?php

declare(strict_types=1);

namespace Netgen\Layouts\Migrations\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Utils\BackwardsCompatibility\Locales;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Version000900 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $defaultLocale = '';
        if ($this->hasLayouts()) {
            $defaultLocale = $this->askDefaultLocale();
        }

        // Fix differences in data in root blocks
        $this->addSql('UPDATE ngbm_block SET parent_id = NULL, placeholder = NULL, position = NULL, config = "[]", parameters = "[]" WHERE parent_id = 0 OR parent_id IS NULL');

        // Layout table translations

        $this->addSql('ALTER TABLE ngbm_layout ADD COLUMN main_locale varchar(191) NOT NULL');

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_layout_translation (
              layout_id int(11) NOT NULL,
              status int(11) NOT NULL,
              locale varchar(191) NOT NULL,
              PRIMARY KEY (layout_id, status, locale),
              CONSTRAINT fk_ngl_layout_translation_layout FOREIGN KEY (layout_id, status)
                REFERENCES ngbm_layout (id, status)
            )
            EOT
        );

        $this->addSql('UPDATE ngbm_layout SET main_locale = :main_locale', ['main_locale' => $defaultLocale], ['main_locale' => Types::STRING]);
        $this->addSql('INSERT INTO ngbm_layout_translation SELECT id, status, main_locale FROM ngbm_layout');

        // Block table translations

        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN translatable tinyint NOT NULL');
        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN main_locale varchar(191) NOT NULL');
        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN always_available tinyint NOT NULL');

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_block_translation (
              block_id int(11) NOT NULL,
              status int(11) NOT NULL,
              locale varchar(191) NOT NULL,
              parameters text NOT NULL,
              PRIMARY KEY (block_id, status, locale),
              CONSTRAINT fk_ngl_block_translation_block FOREIGN KEY (block_id, status)
                REFERENCES ngbm_block (id, status)
            )
            EOT
        );

        $this->addSql('UPDATE ngbm_block SET translatable = 0, always_available = 1');
        $this->addSql('UPDATE ngbm_block SET main_locale = :main_locale', ['main_locale' => $defaultLocale], ['main_locale' => Types::STRING]);
        $this->addSql('INSERT INTO ngbm_block_translation SELECT id, status, main_locale, parameters FROM ngbm_block');

        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN parameters');

        // Collection table translations

        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN translatable tinyint NOT NULL');
        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN main_locale varchar(191) NOT NULL');
        $this->addSql('ALTER TABLE ngbm_collection ADD COLUMN always_available tinyint NOT NULL');

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_collection_translation (
              collection_id int(11) NOT NULL,
              status int(11) NOT NULL,
              locale varchar(191) NOT NULL,
              PRIMARY KEY (collection_id, status, locale),
              CONSTRAINT fk_ngl_collection_translation_collection FOREIGN KEY (collection_id, status)
                REFERENCES ngbm_collection (id, status)
            )
            EOT
        );

        $this->addSql('UPDATE ngbm_collection SET translatable = 0, always_available = 1');
        $this->addSql('UPDATE ngbm_collection SET main_locale = :main_locale', ['main_locale' => $defaultLocale], ['main_locale' => Types::STRING]);
        $this->addSql('INSERT INTO ngbm_collection_translation SELECT id, status, main_locale FROM ngbm_collection');

        // Collection query table translations

        $this->addSql(
            <<<'EOT'
            CREATE TABLE ngbm_collection_query_translation (
              query_id int(11) NOT NULL,
              status int(11) NOT NULL,
              locale varchar(191) NOT NULL,
              parameters text NOT NULL,
              PRIMARY KEY (query_id, status, locale),
              CONSTRAINT fk_ngl_query_translation_query FOREIGN KEY (query_id, status)
                REFERENCES ngbm_collection_query (id, status)
            )
            EOT
        );

        $this->addSql('INSERT INTO ngbm_collection_query_translation SELECT id, status, "", parameters FROM ngbm_collection_query');
        $this->addSql('UPDATE ngbm_collection_query_translation SET locale = :locale', ['locale' => $defaultLocale], ['locale' => Types::STRING]);

        $this->addSql('ALTER TABLE ngbm_collection_query DROP COLUMN parameters');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        // Collection query table

        $this->addSql('ALTER TABLE ngbm_collection_query ADD COLUMN parameters text NOT NULL AFTER type');

        $this->addSql('UPDATE ngbm_collection_query q INNER JOIN ngbm_collection c ON q.collection_id = c.id AND q.status = c.status INNER JOIN ngbm_collection_query_translation qt ON q.id = qt.query_id AND q.status = qt.status AND c.main_locale = qt.locale SET q.parameters = qt.parameters');

        $this->addSql('DROP TABLE ngbm_collection_query_translation');

        // Collection table

        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN translatable');
        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN main_locale');
        $this->addSql('ALTER TABLE ngbm_collection DROP COLUMN always_available');

        $this->addSql('DROP TABLE ngbm_collection_translation');

        // Block table

        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN parameters text NOT NULL AFTER config');

        $this->addSql('UPDATE ngbm_block b INNER JOIN ngbm_block_translation bt ON b.id = bt.block_id AND b.status = bt.status AND b.main_locale = bt.locale SET b.parameters = bt.parameters');

        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN translatable');
        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN main_locale');
        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN always_available');

        $this->addSql('DROP TABLE ngbm_block_translation');

        // Layout table

        $this->addSql('ALTER TABLE ngbm_layout DROP COLUMN main_locale');

        $this->addSql('DROP TABLE ngbm_layout_translation');
    }

    /**
     * Returns if the database already contains some layouts.
     */
    private function hasLayouts(): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('COUNT(id) as count')
            ->from('ngbm_layout');

        $result = $queryBuilder->execute()->fetchAllAssociative();

        return (int) $result[0]['count'] > 0;
    }

    /**
     * Asks the user for default layout locale and returns it.
     */
    private function askDefaultLocale(): string
    {
        $io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

        return $io->ask(
            'Please input the default locale for existing layouts',
            '',
            static function (string $locale): string {
                if (!Locales::exists($locale)) {
                    throw new RuntimeException('Specified locale is not valid');
                }

                return $locale;
            },
        );
    }
}
