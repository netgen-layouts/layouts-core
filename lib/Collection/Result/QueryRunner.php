<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Item\ItemBuilderInterface;

final class QueryRunner implements QueryRunnerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    public function __construct(ItemBuilderInterface $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }

    public function runQuery(Query $query, int $offset = 0, int $limit = null): Iterator
    {
        $queryValues = $query->getQueryType()->getValues($query, $offset, $limit);

        foreach ($queryValues as $queryValue) {
            yield $this->itemBuilder->build($queryValue);
        }
    }

    public function count(Query $query): int
    {
        return $query->getQueryType()->getCount($query);
    }
}
