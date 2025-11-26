<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Iterator;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Item\VisibilityResolverInterface;

/**
 * Manual collection behaves as a list where invisible or invalid items
 * DO NOT cause the list to break/stop processing, as they are merely
 * ignored and next item is taken. Provided offset and limit take this
 * into account and act as offset and limit for *valid items*.
 */
final class ManualCollectionRunner implements CollectionRunnerInterface
{
    public function __construct(
        private VisibilityResolverInterface $visibilityResolver,
    ) {}

    public function runCollection(Collection $collection, int $offset, int $limit, int $flags = 0): Iterator
    {
        $itemCount = 0;
        $skippedCount = 0;

        foreach ($collection->items as $collectionItem) {
            if ($itemCount >= $limit) {
                return;
            }

            $includeResult = false;
            $itemValid = $this->visibilityResolver->isVisible($collectionItem) && $collectionItem->isValid;

            if ($itemValid && $skippedCount >= $offset) {
                // We're always including valid items once we skip up to $offset visible items
                // These are the items that are actually displayed on the frontend.
                $includeResult = true;
            }

            if (!$itemValid && $collectionItem->position >= $offset) {
                // We're including all invalid items located after the offset.
                // These are included only if provided flags allow them to be included
                // and these are the ones that could've been displayed if no invalid
                // items exist. These are usually displayed in the backend interface
                // where it is important to know if hidden/invalid items were excluded
                // from displaying at frontend due to their invisibility/invalidity.
                $includeResult = (bool) ($flags & ResultSet::INCLUDE_INVALID_ITEMS);
            }

            if ($includeResult) {
                yield new Result(
                    $collectionItem->position,
                    new ManualItem($collectionItem),
                    null,
                    $collection->getSlot($collectionItem->position),
                );
            }

            if ($itemValid) {
                $skippedCount < $offset ? ++$skippedCount : ++$itemCount;
            }
        }
    }

    public function count(Collection $collection): int
    {
        $totalCount = 0;

        foreach ($collection->items as $collectionItem) {
            if (!$this->visibilityResolver->isVisible($collectionItem) || !$collectionItem->isValid) {
                continue;
            }

            ++$totalCount;
        }

        return $totalCount;
    }
}
