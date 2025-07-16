<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Command\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Generator;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;
use function iterator_to_array;
use function json_decode;
use function sprintf;

final class MigrateQueryOffsetLimitCommand extends Command
{
    private const KNOWN_QUERY_TYPES = [
        'ezcontent_search' => [
            'offset' => 'offset',
            'limit' => 'limit',
        ],
        'ezcontent_relation_list' => [
            'offset' => 'offset',
            'limit' => 'limit',
        ],
        'ezcontent_tags' => [
            'offset' => 'offset',
            'limit' => 'limit',
        ],
        'google_analytics' => [
            'offset' => null,
            'limit' => 'limit',
        ],
        'contentful_search' => [
            'offset' => 'offset',
            'limit' => 'limit',
        ],
    ];

    private QueryTypeRegistry $queryTypeRegistry;

    private Connection $connection;

    private InputInterface $input;

    private OutputInterface $output;

    private SymfonyStyle $io;

    public function __construct(QueryTypeRegistry $queryTypeRegistry, Connection $connection)
    {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->connection = $connection;

        // Parent constructor call is mandatory in commands registered as services
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migrates the query offset and limit parameters to collection offset and limit after upgrade to version 0.10.')
            ->setHidden(true);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;

        $this->io = new SymfonyStyle($this->input, $this->output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Netgen Layouts 0.10 migration script');

        $this->io->block(
            sprintf(
                "%s\n%s\n%s",
                'This script will ask you for names of offset and limit parameters for each of your query types.',
                'Once you answer the questions for all query types, the migration will start.',
                'You will have a choice to cancel the script before starting the migration.',
            ),
        );

        $this->io->caution(
            sprintf(
                "%s\n%s",
                'This script will perform changes to data in your database.',
                'Make sure to backup your database before running the script in case any errors occur.',
            ),
        );

        if (!$this->io->confirm('Did you backup your database? Answering NO will cancel the script', false)) {
            return 1;
        }

        $queryTypes = $this->queryTypeRegistry->getQueryTypes();
        $queryTypeParameters = [];

        foreach ($queryTypes as $queryType) {
            $queryTypeIdentifier = $queryType->getType();
            if (isset(self::KNOWN_QUERY_TYPES[$queryTypeIdentifier])) {
                $queryTypeParameters[$queryTypeIdentifier] = self::KNOWN_QUERY_TYPES[$queryTypeIdentifier];

                continue;
            }

            do {
                $mapping = iterator_to_array($this->askForOffsetAndLimitParameter($queryType));
            } while (
                !$this->io->confirm(
                    ($mapping['offset'] !== null ?
                        "Your '{$queryTypeIdentifier}' query type has an offset parameter named '{$mapping['offset']}'.\n" :
                        "Your '{$queryTypeIdentifier}' query type DOES NOT have an offset parameter.\n") .
                    ($mapping['limit'] !== null ?
                        " Your '{$queryTypeIdentifier}' query type has a limit parameter named '{$mapping['limit']}'.\n" :
                        " Your '{$queryTypeIdentifier}' query type DOES NOT have a limit parameter.\n") .
                    ' Is this correct?',
                    true,
                )
            );

            $queryTypeParameters[$queryTypeIdentifier] = $mapping;
        }

        if (!$this->io->confirm('Do you want to start the migration now?', true)) {
            return 1;
        }

        $this->migrateOffsetAndLimit($queryTypeParameters);

        $this->io->success('Migration done. Now edit all your custom query types to remove the offset and limit parameters.');

        return 0;
    }

    /**
     * Returns the array that maps the names of the offset and limit parameters from
     * the query type.
     *
     * Returned array is in format:
     *
     * [
     *     'offset' => 'OFFSET_PARAM_NAME',
     *     'limit' => 'LIMIT_PARAM_NAME',
     * ]
     *
     * Each of those can be `null` to indicate that the query type does not have an offset
     * or a limit parameter.
     *
     * @return \Generator<string, string>
     */
    private function askForOffsetAndLimitParameter(QueryTypeInterface $queryType): Generator
    {
        $queryTypeParameters = iterator_to_array($this->getQueryTypeParameters($queryType));
        $queryTypeParameters[] = 'NO PARAMETER';

        foreach (['offset', 'limit'] as $parameter) {
            $parameterName = $this->io->choice(
                sprintf(
                    'Select the %1$s parameter from the "%2$s" (%3$s) query type (Use "NO PARAMETER" option if your query type does not have the %1$s parameter)',
                    $parameter,
                    $queryType->getType(),
                    $queryType->getName(),
                ),
                $queryTypeParameters,
            );

            yield $parameter => $parameterName !== 'NO PARAMETER' ? $parameterName : null;
        }
    }

    /**
     * Returns the list of all parameter names from the query type.
     *
     * Considers if the parameter is a compound and includes it's sub-parameters too.
     *
     * @return \Generator<string>
     */
    private function getQueryTypeParameters(QueryTypeInterface $queryType): Generator
    {
        foreach ($queryType->getParameterDefinitions() as $parameterDefinition) {
            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $innerParameterDefinition) {
                    yield $innerParameterDefinition->getName();
                }

                continue;
            }

            yield $parameterDefinition->getName();
        }
    }

    /**
     * @param array<string, mixed> $queryTypeParameters
     */
    private function migrateOffsetAndLimit(array $queryTypeParameters): void
    {
        $queryData = $this->getQueryData();

        $this->io->progressStart(count($queryData));

        $this->connection->transactional(
            function () use ($queryTypeParameters, $queryData): void {
                foreach ($queryData as $queryDataItem) {
                    $offset = 0;
                    $limit = null;

                    $parameters = json_decode($queryDataItem['parameters'], true);
                    if ($queryTypeParameters[$queryDataItem['type']]['offset'] !== null) {
                        $offsetParameter = $queryTypeParameters[$queryDataItem['type']]['offset'];
                        $offset = (int) ($parameters[$offsetParameter] ?? 0);
                    }

                    if ($queryTypeParameters[$queryDataItem['type']]['limit'] !== null) {
                        $limitParameter = $queryTypeParameters[$queryDataItem['type']]['limit'];
                        $limit = isset($parameters[$limitParameter]) ? (int) $parameters[$limitParameter] : null;
                    }

                    $this->updateCollection((int) $queryDataItem['id'], (int) $queryDataItem['status'], $offset, $limit);

                    $this->io->progressAdvance();
                }
            },
        );

        $this->io->progressFinish();
    }

    /**
     * Returns all query data required to migrate offset and limit to the collections.
     *
     * @return mixed[]
     */
    private function getQueryData(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('c.id AS id, c.status AS status, q.type AS type, qt.parameters AS parameters')
            ->from('nglayouts_collection', 'c')
            ->innerJoin(
                'c',
                'nglayouts_collection_query',
                'q',
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('c.id', 'q.collection_id'),
                    $queryBuilder->expr()->eq('c.status', 'q.status'),
                ),
            )
            ->innerJoin(
                'q',
                'nglayouts_collection_query_translation',
                'qt',
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('q.id', 'qt.query_id'),
                    $queryBuilder->expr()->eq('q.status', 'qt.status'),
                    $queryBuilder->expr()->eq('c.main_locale', 'qt.locale'),
                ),
            );

        return $queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * Sets the provided offset and limit to the collection.
     */
    private function updateCollection(int $id, int $status, int $offset, ?int $limit = null): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->update('nglayouts_collection')
            ->set('start', ':start')
            ->set('length', ':length')
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('id', ':id'),
                    $queryBuilder->expr()->eq('status', ':status'),
                ),
            )
            ->setParameter('id', $id, Types::INTEGER)
            ->setParameter('status', $status, Types::INTEGER)
            ->setParameter('start', $offset, Types::INTEGER)
            ->setParameter('length', $limit, Types::INTEGER);

        $queryBuilder->execute();
    }
}
