<?php

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

    /**
     * @param \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory $runnerFactory
     * @param int $contextualLimit
     * @param int $maxLimit
     */
    public function __construct(
        CollectionRunnerFactory $runnerFactory,
        $contextualLimit,
        $maxLimit
    ) {
        $this->runnerFactory = $runnerFactory;
        $this->contextualLimit = $contextualLimit;
        $this->maxLimit = $maxLimit;
    }

    public function build(Collection $collection, $offset = 0, $limit = null, $flags = 0)
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

        $results = array();
        $collectionCount = $collectionRunner->count($collection);
        if ($limit > 0 && $offset < $collectionCount) {
            $results = call_user_func($collectionRunner, $collection, $offset, $limit);
            $results = iterator_to_array($results);
        }

        return new ResultSet(
            array(
                'collection' => $collection,
                'results' => $results,
                'totalCount' => $collectionCount,
                'offset' => $offset,
                'limit' => $limit,
            )
        );
    }
}
