<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

/**
 * Manual collection behaves as a list where invisible or invalid items
 * DO NOT cause the list to break/stop processing, as they are merely
 * ignored and next item is taken. Provided offset and limit take this
 * into account and act as offset and limit for *valid items*.
 */
final class ManualCollectionRunner implements CollectionRunnerInterface
{
    public function runCollection(Collection $collection, $offset, $limit, $flags = 0)
    {
        $itemCount = 0;
        $skippedCount = 0;

        foreach ($collection->getItems() as $collectionItem) {
            if ($itemCount >= $limit) {
                return;
            }

            $includeResult = false;
            $itemValid = $collectionItem->isValid();

            if ($itemValid && $skippedCount >= $offset) {
                // We're always including valid items once we skip up to offset visible items
                // These are the items that are actually displayed on the frontend.
                $includeResult = true;
            }

            if (!$itemValid && $collectionItem->getPosition() >= $offset) {
                // We're including all invalid items located after the offset.
                // These are included only if provided flags allow them to be included
                // and these are the ones that could've been displayed if no invalid
                // items exist. These are usually displayed in the backend interface
                // where it is important to know if hidden/invalid items were excluded
                // from displaying at frontend due to their invisibility/invalidity.
                $includeResult = (bool) ($flags & ResultSet::INCLUDE_INVALID_ITEMS);
            }

            if ($includeResult) {
                yield new Result($collectionItem->getPosition(), new ManualItem($collectionItem));
            }

            if ($itemValid) {
                $skippedCount < $offset ? ++$skippedCount : ++$itemCount;
            }
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
