<?php

namespace Netgen\Bundle\BlockManagerBundle\Command\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrateQueryOffsetLimitCommand extends Command
{
    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    private $queryTypeRegistry;

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
    private $style;

    public function __construct(QueryTypeRegistryInterface $queryTypeRegistry, Connection $connection)
    {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->connection = $connection;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ngbm:migrate:query_offset_limit')
            ->setDescription('Migrates the query offset and limit parameters to collection offset and limit after upgrade to version 0.10.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->style = new SymfonyStyle($this->input, $this->output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->style->title('Netgen Layouts 0.10 migration script');

        $this->style->block(
            sprintf(
                "%s\n%s\n%s",
                'This script will ask you for names of offset and limit parameters for each of your query types.',
                'Once you answer the questions for all query types, the migration will start.',
                'You will have a choice to cancel the script before starting the migration.'
            )
        );

        $this->style->caution(
            sprintf(
                "%s\n%s",
                'This script will migrate values of offset and limit parameters from your query types to its collection.',
                'Make sure to backup your database before running the script in case any errors occur.'
            )
        );

        if (!$this->style->confirm('Did you backup your database? Answering NO will cancel the script', false)) {
            return 1;
        }

        $queryTypes = $this->queryTypeRegistry->getQueryTypes();
        $queryTypeParameters = array();

        foreach ($queryTypes as $queryType) {
            do {
                $mapping = $this->askForOffsetAndLimitParameter($queryType);
            } while (
                !$this->style->confirm(
                    sprintf(
                        ($mapping['offset'] !== null ?
                            "Your '%1\$s' query type has an offset parameter named '%2\$s'\n" :
                            "Your '%1\$s' query type DOES NOT have an offset parameter\n") .
                        ($mapping['limit'] !== null ?
                            "Your '%1\$s' query type has a limit parameter named '%3\$s'\n" :
                            "Your '%1\$s' query type DOES NOT have a limit parameter\n") .
                        'Is this correct?',
                        $queryType->getType(),
                        $mapping['offset'],
                        $mapping['limit']
                    ),
                    true
                )
            );

            $queryTypeParameters[$queryType->getType()] = $mapping;
        }

        if (!$this->style->confirm('Do you want to start the migration now?', true)) {
            return 1;
        }

        $this->migrateOffsetAndLimit($queryTypeParameters);

        $this->style->success('Migration done. Now edit all your query types to remove the offset and limit parameters.');

        return 0;
    }

    /**
     * Returns the array that maps the names of the offset and limit parameters from
     * the query type.
     *
     * Returned array is in format:
     *
     * array(
     *     'offset' => 'OFFSET_PARAM_NAME',
     *     'limit' => 'LIMIT_PARAM_NAME',
     * )
     *
     * Each of those can be `null` to indicate that the query type does not have an offset
     * or a limit parameter.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     *
     * @return array
     */
    private function askForOffsetAndLimitParameter(QueryTypeInterface $queryType)
    {
        $mapping = array();

        $queryTypeParameters = $this->getQueryTypeParameters($queryType);
        $queryTypeParameters[] = 'NO PARAMETER';

        foreach (array('offset', 'limit') as $parameter) {
            $parameterName = $this->style->choice(
                sprintf(
                    'Select the %1$s parameter from the "%2$s" (%3$s) query type (Use "NO PARAMETER" option if your query type does not have the %1$s parameter)',
                    $parameter,
                    $queryType->getType(),
                    $queryType->getConfig()->getName()
                ),
                $queryTypeParameters
            );

            $mapping[$parameter] = $parameterName !== 'NO PARAMETER' ? $parameterName : null;
        }

        return $mapping;
    }

    /**
     * Returns the list of all parameter names from the query type.
     *
     * Considers if the parameter is a compound and includes it's sub-parameters too.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     *
     * @return array
     */
    private function getQueryTypeParameters(QueryTypeInterface $queryType)
    {
        $parameters = array();

        foreach ($queryType->getParameters() as $parameter) {
            if ($parameter instanceof CompoundParameterInterface) {
                foreach ($parameter->getParameters() as $innerParameter) {
                    $parameters[] = $innerParameter->getName();
                }

                continue;
            }

            $parameters[] = $parameter->getName();
        }

        return $parameters;
    }

    private function migrateOffsetAndLimit(array $queryTypeParameters)
    {
        $queryData = $this->getQueryData();

        $this->style->progressStart(count($queryData));

        $this->connection->transactional(function () use ($queryTypeParameters, $queryData) {
            foreach ($queryData as $queryDataItem) {
                $offset = 0;
                $limit = null;

                $parameters = json_decode($queryDataItem['parameters'], true);
                if (!empty($queryTypeParameters[$queryDataItem['type']]['offset'])) {
                    $offsetParameter = $queryTypeParameters[$queryDataItem['type']]['offset'];
                    $offset = isset($parameters[$offsetParameter]) ? (int) $parameters[$offsetParameter] : 0;
                }

                if (!empty($queryTypeParameters[$queryDataItem['type']]['limit'])) {
                    $limitParameter = $queryTypeParameters[$queryDataItem['type']]['limit'];
                    $limit = isset($parameters[$limitParameter]) ? (int) $parameters[$limitParameter] : null;
                }

                $this->updateCollection($queryDataItem['id'], $queryDataItem['status'], $offset, $limit);

                $this->style->progressAdvance();
            }
        });

        $this->style->progressFinish();
    }

    /**
     * Returns all query data required to migrate offset and limit to the collections.
     *
     * @return array
     */
    private function getQueryData()
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('c.id AS id, c.status AS status, q.type AS type, qt.parameters AS parameters')
            ->from('ngbm_collection', 'c')
            ->innerJoin(
                'c',
                'ngbm_collection_query',
                'q',
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.id', 'q.collection_id'),
                    $queryBuilder->expr()->eq('c.status', 'q.status')
                )
            )
            ->innerJoin(
                'q',
                'ngbm_collection_query_translation',
                'qt',
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('q.id', 'qt.query_id'),
                    $queryBuilder->expr()->eq('q.status', 'qt.status'),
                    $queryBuilder->expr()->eq('c.main_locale', 'qt.locale')
                )
            );

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Sets the provided offset and limit to the collection.
     *
     * @param int|string $id
     * @param int $status
     * @param int $offset
     * @param int $limit
     */
    private function updateCollection($id, $status, $offset, $limit)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->update('ngbm_collection')
            ->set('start', ':start')
            ->set('length', ':length')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('id', ':id'),
                    $queryBuilder->expr()->eq('status', ':status')
                )
            )
            ->setParameter('id', $id, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER)
            ->setParameter('start', $offset, Type::INTEGER)
            ->setParameter('length', $limit, Type::INTEGER);

        $queryBuilder->execute();
    }
}
