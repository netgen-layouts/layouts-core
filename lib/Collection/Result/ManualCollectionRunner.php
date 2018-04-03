<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

final class ManualCollectionRunner implements CollectionRunnerInterface
{
    public function __invoke(Collection $collection, $offset, $limit)
    {
        $itemCount = 0;

        foreach ($collection->getItems() as $collectionItem) {
            if ($itemCount >= $limit) {
                return;
            }

            if ($collectionItem->getPosition() < $offset) {
                continue;
            }

            if (!$collectionItem->isValid()) {
                continue;
            }

            yield new Result($collectionItem->getPosition(), new ManualItem($collectionItem));

            ++$itemCount;
        }
    }

    public function count(Collection $collection)
    {
        $totalCount = 0;

        foreach ($collection->getItems() as $collectionItem) {
            if (!$collectionItem->isValid()) {
                continue;
            }

            ++$totalCount;
        }

        return $totalCount;
    }
}
