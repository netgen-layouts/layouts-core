<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use ArrayIterator;
use Iterator;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item as CollectionItem;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Item\VisibilityResolverInterface;
use Netgen\Layouts\Item\CmsItemInterface;

final class DynamicCollectionRunner implements CollectionRunnerInterface
{
    private QueryRunnerInterface $queryRunner;

    private VisibilityResolverInterface $visibilityResolver;

    public function __construct(QueryRunnerInterface $queryRunner, VisibilityResolverInterface $visibilityResolver)
    {
        $this->queryRunner = $queryRunner;
        $this->visibilityResolver = $visibilityResolver;
    }

    public function runCollection(Collection $collection, int $offset, int $limit, int $flags = 0): Iterator
    {
        $queryIterator = $this->runQuery($collection, $offset, $limit);

        for ($i = $offset, $max = $offset + $limit; $i < $max; ++$i) {
            $result = null;

            $collectionItem = $collection->getItem($i);

            if ($collectionItem instanceof CollectionItem) {
                $result = $this->buildManualResult($collection, $collectionItem, $queryIterator);
            } elseif ($queryIterator->valid()) {
                $result = new Result($i, $this->getQueryValue($queryIterator), null, $collection->getSlot($i));
            }

            if (!$result instanceof Result) {
                return;
            }

            yield $result;
        }
    }

    public function count(Collection $collection): int
    {
        $totalCount = 0;

        $collectionQuery = $collection->getQuery();
        if ($collectionQuery instanceof Query) {
            $totalCount = $this->queryRunner->count($collectionQuery);
        }

        foreach ($collection->getItems() as $item) {
            if ($item->getPosition() > $totalCount) {
                break;
            }

            if ($this->visibilityResolver->isVisible($item) && $item->isValid()) {
                ++$totalCount;
            }
        }

        return $totalCount;
    }

    /**
     * Builds the result from a manual item.
     *
     * When manual items are invisible or invalid, they are pushed to the subitem role,
     * and the item which is displayed is the next query value.
     *
     * @param \Iterator<\Netgen\Layouts\Item\CmsItemInterface> $queryIterator
     */
    private function buildManualResult(Collection $collection, CollectionItem $collectionItem, Iterator $queryIterator): ?Result
    {
        if (!$this->visibilityResolver->isVisible($collectionItem) || !$collectionItem->isValid()) {
            $queryValue = $this->getQueryValue($queryIterator);
            if (!$queryValue instanceof CmsItemInterface) {
                return null;
            }

            return new Result(
                $collectionItem->getPosition(),
                $queryValue,
                new ManualItem($collectionItem),
                $collection->getSlot($collectionItem->getPosition()),
            );
        }

        return new Result(
            $collectionItem->getPosition(),
            new ManualItem($collectionItem),
            null,
            $collection->getSlot($collectionItem->getPosition()),
        );
    }

    /**
     * Returns the current value from the query and advances the iterator.
     *
     * @param \Iterator<\Netgen\Layouts\Item\CmsItemInterface> $queryIterator
     *
     * @return mixed
     */
    private function getQueryValue(Iterator $queryIterator)
    {
        if (!$queryIterator->valid()) {
            return null;
        }

        $queryValue = $queryIterator->current();
        $queryIterator->next();

        return $queryValue;
    }

    /**
     * Returns the iterator that can be used to iterate over provided collection query.
     *
     * @return \Iterator<\Netgen\Layouts\Item\CmsItemInterface>
     */
    private function runQuery(Collection $collection, int $offset, int $limit): Iterator
    {
        $collectionQuery = $collection->getQuery();
        if (!$collectionQuery instanceof Query) {
            return new ArrayIterator();
        }

        $queryOffset = $offset - $this->getManualItemsCount($collection, 0, $offset);
        $queryLimit = $limit - $this->getManualItemsCount($collection, $offset, $offset + $limit);

        return $this->queryRunner->runQuery($collectionQuery, $queryOffset, $queryLimit);
    }

    /**
     * Returns the count of valid manual items in a collection between $startOffset and $endOffset.
     */
    private function getManualItemsCount(Collection $collection, int $startOffset, int $endOffset): int
    {
        $manualItemsCount = 0;

        foreach ($collection->getItems() as $item) {
            $itemPosition = $item->getPosition();
            if ($itemPosition < $startOffset || $itemPosition >= $endOffset) {
                continue;
            }

            if ($this->visibilityResolver->isVisible($item) && $item->isValid()) {
                ++$manualItemsCount;
            }
        }

        return $manualItemsCount;
    }
}
