<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use RuntimeException;

class QueryRunner implements QueryRunnerInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(QueryTypeRegistryInterface $queryTypeRegistry)
    {
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Runs all the provided queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query[] $queries
     * @param int $offset
     * @param int $limit
     *
     * @throws \RuntimeException If there are no queries to run
     *
     * @return mixed
     */
    public function runQueries(array $queries, $offset = 0, $limit = null)
    {
        if (empty($queries)) {
            throw new RuntimeException('There are no queries to run.');
        }

        if (count($queries) === 1) {
            return $this->runSingleQuery($queries[0], $offset, $limit);
        }

        return $this->runMultipleQueries($queries, $offset, $limit);
    }

    /**
     * Runs a single query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    protected function runSingleQuery(Query $query, $offset = 0, $limit = null)
    {
        return $this->queryTypeRegistry->getQueryType($query->getType())->getValues(
            $query->getParameters(),
            $offset,
            $limit
        );
    }

    /**
     * Runs all the provided queries and merges result into one list.
     *
     * Considers all the queries as one big list for applying offset and limit.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query[] $queries
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    protected function runMultipleQueries(array $queries, $offset = 0, $limit = null)
    {
        $count = 0;
        $values = array();

        foreach ($queries as $query) {
            $queryType = $query->getType();
            $queryParameters = $query->getParameters();

            $queryCount = $this->queryTypeRegistry
                ->getQueryType($queryType)
                ->getCount($queryParameters);

            $count += $queryCount;
            if ($count <= $offset) {
                continue;
            }

            $queryValues = $this->queryTypeRegistry->getQueryType($queryType)->getValues(
                $queryParameters,
                empty($values) ? $count - $offset : 0
            );

            $values = array_merge($values, $queryValues);
            if ($limit !== null && count($values) >= $limit) {
                $values = array_slice($values, 0, $limit);
                break;
            }
        }

        return $values;
    }
}
