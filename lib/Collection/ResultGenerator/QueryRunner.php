<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

class QueryRunner implements QueryRunnerInterface
{
    /**
     * Runs all the provided queries and merges result into one list.
     *
     * Considers all the queries as one big list for applying offset and limit.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query[] $queries
     * @param int $offset
     * @param int $limit
     * @param bool $includeInvisible
     *
     * @return mixed
     */
    public function runQueries(array $queries, $offset = 0, $limit = null, $includeInvisible = false)
    {
        if (empty($queries)) {
            return array();
        }

        $previousCount = 0;
        $values = array();

        foreach ($queries as $query) {
            $queryTypeHandler = $query->getQueryType()->getHandler();
            $queryParameters = $query->getParameters();

            $queryCount = $queryTypeHandler->getCount($queryParameters);

            $totalCount = $previousCount + $queryCount;
            if ($previousCount + $queryCount <= $offset) {
                $previousCount = $totalCount;
                continue;
            }

            $queryValues = $queryTypeHandler->getValues(
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

    /**
     * Returns the total count of all queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query[] $queries
     * @param bool $includeInvisible
     *
     * @return int
     */
    public function getTotalCount(array $queries, $includeInvisible = false)
    {
        if (empty($queries)) {
            return 0;
        }

        $totalCount = 0;
        foreach ($queries as $query) {
            $totalCount += $query
                ->getQueryType()
                ->getHandler()
                ->getCount($query->getParameters());
        }

        return $totalCount;
    }
}
