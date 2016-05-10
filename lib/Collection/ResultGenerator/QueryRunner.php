<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

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
     * Runs all the provided queries and merges result into one list.
     *
     * Considers all the queries as one big list for applying offset and limit.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query[] $queries
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Collection\ResultValue[]
     */
    public function runQueries(array $queries, $offset = 0, $limit = null)
    {
        if (empty($queries)) {
            throw new RuntimeException('There are no queries to run.');
        }

        $previousCount = 0;
        $values = array();

        foreach ($queries as $query) {
            $queryType = $query->getType();
            $queryParameters = $query->getParameters();

            $queryCount = $this->queryTypeRegistry
                ->getQueryType($queryType)
                ->getCount($queryParameters);

            $totalCount = $previousCount + $queryCount;
            if ($previousCount + $queryCount <= $offset) {
                $previousCount = $totalCount;
                continue;
            }

            $queryValues = $this->queryTypeRegistry->getQueryType($queryType)->getValues(
                $queryParameters,
                empty($values) && $offset > 0 ? $offset - $previousCount : 0
            );

            $values = array_merge($values, $queryValues);
            if ($limit !== null && count($values) >= $limit) {
                $values = array_slice($values, 0, $limit);
                break;
            }

            $previousCount = $totalCount;
        }

        return $values;
    }
}
