<?php

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

    /**
     * @var int
     */
    private $contextualLimit;

    public function __construct(ItemBuilderInterface $itemBuilder, $contextualLimit)
    {
        $this->itemBuilder = $itemBuilder;
        $this->contextualLimit = $contextualLimit;
    }

    /**
     * Builds and returns the collection runner for provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\CollectionRunnerInterface
     */
    public function getCollectionRunner(Collection $collection, $flags = 0)
    {
        if ($collection->hasQuery()) {
            $queryRunner = $this->getQueryRunner($collection->getQuery(), $flags);

            return new DynamicCollectionRunner($queryRunner);
        }

        return new ManualCollectionRunner();
    }

    /**
     * Builds the query runner for the provided query based on provided flags.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\QueryRunnerInterface
     */
    private function getQueryRunner(Query $query, $flags = 0)
    {
        $showContextualSlots = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);

        if ($query->isContextual() && $showContextualSlots) {
            return new ContextualQueryRunner($this->contextualLimit);
        }

        return new QueryRunner($this->itemBuilder);
    }
}
