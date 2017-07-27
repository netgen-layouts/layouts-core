<?php

namespace Netgen\BlockManager\Migrations\Doctrine;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Intl\Intl;

class Version000900 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

        $defaultLocale = $io->ask(
            'Please input the default locale for existing layouts',
            null,
            function ($locale) {
                if (Intl::getLocaleBundle()->getLocaleName($locale) === null) {
                    throw new RuntimeException('Specified locale is not valid');
                }

                return $locale;
            }
        );

        // Fix differences in data in root blocks
        $this->addSql('UPDATE ngbm_block SET parent_id = NULL, placeholder = NULL, position = NULL, config = "[]", parameters = "[]" WHERE parent_id = 0 OR parent_id IS NULL');

        $this->addSql('ALTER TABLE ngbm_layout ADD COLUMN main_locale varchar(255) NOT NULL');

        $this->addSql(<<<'EOT'
CREATE TABLE `ngbm_layout_translation` (
  `layout_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY (`layout_id`, `status`, `locale`),
  FOREIGN KEY (`layout_id`, `status`)
    REFERENCES ngbm_layout (`id`, `status`)
)
EOT
);

        $this->addSql('UPDATE ngbm_layout SET main_locale = :main_locale', array('main_locale' => $defaultLocale), array('main_locale' => Type::STRING));
        $this->addSql('INSERT INTO ngbm_layout_translation SELECT id, status, main_locale FROM ngbm_layout');

        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN translatable tinyint NOT NULL');
        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN main_locale varchar(255) NOT NULL');
        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN always_available tinyint NOT NULL');

        $this->addSql(<<<'EOT'
CREATE TABLE `ngbm_block_translation` (
  `block_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`block_id`, `status`, `locale`),
  FOREIGN KEY (`block_id`, `status`)
    REFERENCES ngbm_block (`id`, `status`)
)
EOT
);

        $this->addSql('UPDATE ngbm_block SET translatable = 0, always_available = 1');
        $this->addSql('UPDATE ngbm_block SET main_locale = :main_locale', array('main_locale' => $defaultLocale), array('main_locale' => Type::STRING));
        $this->addSql('INSERT INTO ngbm_block_translation SELECT id, status, main_locale, parameters FROM ngbm_block');

        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN parameters');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on MySQL.');

        $this->addSql('ALTER TABLE ngbm_block ADD COLUMN parameters text NOT NULL AFTER config');

        $this->addSql('UPDATE ngbm_block b INNER JOIN ngbm_block_translation bt ON b.id = bt.block_id AND b.status = bt.status AND b.main_locale = bt.locale SET b.parameters = bt.parameters');

        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN translatable');
        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN main_locale');
        $this->addSql('ALTER TABLE ngbm_block DROP COLUMN always_available');

        $this->addSql('DROP TABLE ngbm_block_translation');

        $this->addSql('ALTER TABLE ngbm_layout DROP COLUMN main_locale');

        $this->addSql('DROP TABLE ngbm_layout_translation');
    }
}
