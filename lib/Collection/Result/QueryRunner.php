<?php

namespace Netgen\BlockManager\Collection\Result;

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

    public function __invoke(Query $query, $offset = 0, $limit = null)
    {
        $queryValues = $query->getQueryType()->getValues($query, $offset, $limit);

        foreach ($queryValues as $queryValue) {
            yield $this->itemBuilder->build($queryValue);
        }
    }

    public function count(Query $query)
    {
        return $query->getQueryType()->getCount($query);
    }
}
