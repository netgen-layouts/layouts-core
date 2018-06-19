<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Item\ItemBuilderInterface;

final class CollectionRunnerFactory
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    public function __construct(ItemBuilderInterface $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Builds and returns the collection runner for provided collection.
     */
    public function getCollectionRunner(Collection $collection, int $flags = 0): CollectionRunnerInterface
    {
        $collectionQuery = $collection->getQuery();

        if ($collectionQuery instanceof Query) {
            $queryRunner = $this->getQueryRunner($collectionQuery, $flags);

            return new DynamicCollectionRunner($queryRunner);
        }

        return new ManualCollectionRunner();
    }

    /**
     * Builds the query runner for the provided query based on provided flags.
     */
    private function getQueryRunner(Query $query, int $flags = 0): QueryRunnerInterface
    {
        $showContextualSlots = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);

        if ($query->isContextual() && $showContextualSlots) {
            return new ContextualQueryRunner();
        }

        return new QueryRunner($this->itemBuilder);
    }
}
