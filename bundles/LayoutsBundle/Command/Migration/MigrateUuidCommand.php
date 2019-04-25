<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Command\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Netgen\Layouts\API\Values\Value;
use PDO;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrateUuidCommand extends Command
{
    private const NAMESPACE_LAYOUT = 'a3468550-de48-4cc3-818d-7a3350ee5d40';

    private const NAMESPACE_BLOCK = 'a3468551-de48-4cc3-818d-7a3350ee5d40';

    private const NAMESPACE_RULE = 'a3468552-de48-4cc3-818d-7a3350ee5d40';

    private const NAMESPACE_TARGET = 'a3468553-de48-4cc3-818d-7a3350ee5d40';

    private const NAMESPACE_CONDITION = 'a3468554-de48-4cc3-818d-7a3350ee5d40';

    private const NAMESPACE_COLLECTION = 'a3468555-de48-4cc3-818d-7a3350ee5d40';

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $io;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        // Parent constructor call is mandatory in commands registered as services
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generates UUIDs for all records in Netgen Layouts database tables.')
            ->setHidden(true);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;

        $this->io = new SymfonyStyle($this->input, $this->output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io->title('Netgen Layouts UUID migration script');

        $this->io->caution(
            sprintf(
                "%s\n%s",
                'This script will generate UUIDs for all records in Netgen Layouts database tables.',
                'Make sure to backup your database before running the script in case any errors occur.'
            )
        );

        if (!$this->io->confirm('Did you backup your database? Answering NO will cancel the script', false)) {
            return 1;
        }

        $this->updateLayouts();

        $this->updateTable('nglayouts_block', self::NAMESPACE_BLOCK, 'blocks');
        $this->updateTable('nglayouts_rule', self::NAMESPACE_RULE, 'rules');
        $this->updateTable('nglayouts_rule_target', self::NAMESPACE_TARGET, 'targets');
        $this->updateTable('nglayouts_rule_condition', self::NAMESPACE_CONDITION, 'conditions');
        $this->updateTable('nglayouts_collection', self::NAMESPACE_COLLECTION, 'collections');

        $this->io->success('Generating UUIDs done.');

        return 0;
    }

    private function updateLayouts(): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('id, status, name')
            ->from('nglayouts_layout');

        $data = $queryBuilder->execute()->fetchAll(PDO::FETCH_ASSOC);

        $this->io->writeln(['Generating UUIDs for layouts...', '']);
        $progressBar = $this->io->createProgressBar(count($data));

        $layoutNames = [];
        foreach ($data as $layoutData) {
            if ((int) $layoutData['status'] === Value::STATUS_PUBLISHED) {
                $layoutNames[$layoutData['id']] = $layoutData['name'];
            }
        }

        foreach ($data as $layoutData) {
            $uuid = Uuid::uuid5(
                self::NAMESPACE_LAYOUT,
                $layoutNames[$layoutData['id']] ?? $layoutData['name']
            );

            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->update('nglayouts_layout')
                ->set('uuid', ':uuid')
                ->where(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('id', ':id'),
                        $queryBuilder->expr()->eq('status', ':status')
                    )
                )
                ->setParameter('id', $layoutData['id'], Type::INTEGER)
                ->setParameter('status', $layoutData['status'], Type::INTEGER)
                ->setParameter('uuid', $uuid->toString(), Type::STRING);

            $queryBuilder->execute();

            $progressBar->advance();
        }

        $this->io->writeln(['', '']);
    }

    private function updateTable(string $tableName, string $uuidNamespace, string $entityName): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('id, status')
            ->from($tableName);

        $data = $queryBuilder->execute()->fetchAll(PDO::FETCH_ASSOC);

        $this->io->writeln([sprintf('Generating UUIDs for %s...', $entityName), '']);
        $progressBar = $this->io->createProgressBar(count($data));

        foreach ($data as $dataItem) {
            $uuid = Uuid::uuid5($uuidNamespace, $dataItem['id']);

            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->update($tableName)
                ->set('uuid', ':uuid')
                ->where(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('id', ':id'),
                        $queryBuilder->expr()->eq('status', ':status')
                    )
                )
                ->setParameter('id', $dataItem['id'], Type::INTEGER)
                ->setParameter('status', $dataItem['status'], Type::INTEGER)
                ->setParameter('uuid', $uuid->toString(), Type::STRING);

            $queryBuilder->execute();

            $progressBar->advance();
        }

        $this->io->writeln(['', '']);
    }
}
