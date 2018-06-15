<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

/**
 * A builder generates the collection results. Note that in order to disable fetching unlimited
 * number of results (for performance reasons), the number of results is hardcoded to a max limit
 * provided in the constructor.
 */
final class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory
     */
    private $runnerFactory;

    /**
     * @var int
     */
    private $contextualLimit;

    /**
     * @var int
     */
    private $maxLimit;

    public function __construct(
        CollectionRunnerFactory $runnerFactory,
        int $contextualLimit,
        int $maxLimit
    ) {
        $this->runnerFactory = $runnerFactory;
        $this->contextualLimit = $contextualLimit;
        $this->maxLimit = $maxLimit;
    }

    public function build(Collection $collection, int $offset = 0, int $limit = null, int $flags = 0): ResultSet
    {
        $offset = $offset >= 0 ? $offset : 0;
        if ($limit === null || $limit < 0 || $limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        }

        $showContextualSlots = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);
        if ($collection->hasQuery() && $collection->getQuery()->isContextual() && $showContextualSlots) {
            $limit = $limit > 0 && $limit < $this->contextualLimit ? $limit : $this->contextualLimit;
        }

        $collectionRunner = $this->runnerFactory->getCollectionRunner($collection, $flags);

        $results = [];
        $totalCount = $collectionRunner->count($collection);
        if ($limit > 0 && $offset < $totalCount) {
            $results = iterator_to_array(
                $collectionRunner->runCollection($collection, $offset, $limit, $flags)
            );
        }

        return new ResultSet(
            [
                'collection' => $collection,
                'results' => $results,
                'totalCount' => $totalCount,
                'offset' => $offset,
                'limit' => $limit,
            ]
        );
    }
}
