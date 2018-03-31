<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;

final class CollectionRunnerFactory
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    public function __construct(
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder
    ) {
        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
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

            return new DynamicCollectionRunner($this->itemLoader, $queryRunner);
        }

        return new ManualCollectionRunner($this->itemLoader);
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
            return new ContextualQueryRunner();
        }

        return new QueryRunner($this->itemBuilder);
    }
}
