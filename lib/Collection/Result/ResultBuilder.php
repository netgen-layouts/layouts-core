<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;

/**
 * A builder generates the collection results. Note that in order to disable fetching unlimited
 * number of results (for performance reasons), the number of results is hardcoded to a max limit
 * provided in the constructor.
 */
final class ResultBuilder implements ResultBuilderInterface
{
    private CollectionRunnerFactory $runnerFactory;

    private int $contextualLimit;

    private int $maxLimit;

    public function __construct(
        CollectionRunnerFactory $runnerFactory,
        int $contextualLimit,
        int $maxLimit
    ) {
        $this->runnerFactory = $runnerFactory;
        $this->contextualLimit = $contextualLimit;
        $this->maxLimit = $maxLimit;
    }

    public function build(Collection $collection, int $offset = 0, ?int $limit = null, int $flags = 0): ResultSet
    {
        $offset = $offset >= 0 ? $offset : 0;
        if ($limit === null || $limit < 0 || $limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        }

        $collectionQuery = $collection->getQuery();

        $showUnknownItems = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);
        if ($showUnknownItems && $collectionQuery instanceof Query && $collectionQuery->isContextual()) {
            $limit = $limit > 0 && $limit < $this->contextualLimit ? $limit : $this->contextualLimit;
        }

        $collectionRunner = $this->runnerFactory->getCollectionRunner($collection, $flags);

        $results = [];
        $totalCount = $collectionRunner->count($collection);
        if ($limit > 0 && $offset < $totalCount) {
            $results = [...$collectionRunner->runCollection($collection, $offset, $limit, $flags)];
        }

        return ResultSet::fromArray(
            [
                'collection' => $collection,
                'results' => $results,
                'totalCount' => $totalCount,
                'offset' => $offset,
                'limit' => $limit,
            ],
        );
    }
}
