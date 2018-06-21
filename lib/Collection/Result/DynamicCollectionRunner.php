<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Iterator;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Item\CmsItemInterface;

final class DynamicCollectionRunner implements CollectionRunnerInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\QueryRunnerInterface
     */
    private $queryRunner;

    public function __construct(QueryRunnerInterface $queryRunner)
    {
        $this->queryRunner = $queryRunner;
    }

    public function runCollection(Collection $collection, int $offset, int $limit, int $flags = 0): Iterator
    {
        $queryIterator = $this->runQuery($collection, $offset, $limit);

        for ($i = $offset, $max = $offset + $limit; $i < $max; ++$i) {
            $result = null;

            $collectionItem = $collection->getItem($i);

            if ($collectionItem instanceof CollectionItem && $collectionItem->isOverride()) {
                $result = $this->buildOverrideResult($collectionItem, $queryIterator);
            } elseif ($collectionItem instanceof CollectionItem) {
                $result = $this->buildManualResult($collectionItem, $queryIterator);
            } elseif ($queryIterator->valid()) {
                $result = new Result($i, $this->getQueryValue($queryIterator));
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

            if ($item->getType() !== CollectionItem::TYPE_OVERRIDE || $item->getPosition() === $totalCount) {
                if ($item->isValid()) {
                    ++$totalCount;
                }
            }
        }

        return $totalCount;
    }

    /**
     * Builds the result from an override item.
     *
     * This kind of result always has the main item and subitem. However, two cases are possible:
     *
     * 1) When override item is valid, than the subitem is a next value from the query, since
     *    the nature of override item is that it covers the value coming from the query.
     *
     * 2) When override item is not valid, that the item itself is a subitem, and the main
     *    item is a query value, just as it is the case with manual items.
     */
    private function buildOverrideResult(CollectionItem $collectionItem, Iterator $queryIterator): ?Result
    {
        $queryValue = $this->getQueryValue($queryIterator);

        if (!$collectionItem->isValid()) {
            if (!$queryValue instanceof CmsItemInterface) {
                return null;
            }

            return new Result($collectionItem->getPosition(), $queryValue, new ManualItem($collectionItem));
        }

        return new Result($collectionItem->getPosition(), new ManualItem($collectionItem), $queryValue);
    }

    /**
     * Builds the result from a manual item.
     *
     * When manual items are invisible or invalid, they are pushed to the subitem role,
     * and the item which is displayed is the next query value.
     */
    private function buildManualResult(CollectionItem $collectionItem, Iterator $queryIterator): ?Result
    {
        if (!$collectionItem->isValid()) {
            $queryValue = $this->getQueryValue($queryIterator);
            if (!$queryValue instanceof CmsItemInterface) {
                return null;
            }

            return new Result($collectionItem->getPosition(), $queryValue, new ManualItem($collectionItem));
        }

        return new Result($collectionItem->getPosition(), new ManualItem($collectionItem));
    }

    /**
     * Returns the current value from the query and advances the iterator.
     *
     * @param \Iterator $queryIterator
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

        foreach ($collection->getManualItems() as $item) {
            if ($item->getPosition() < $startOffset || $item->getPosition() >= $endOffset) {
                continue;
            }

            if ($item->isValid()) {
                ++$manualItemsCount;
            }
        }

        return $manualItemsCount;
    }
}
