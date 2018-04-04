<?php

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\ItemInterface as CmsItem;

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

    public function __invoke(Collection $collection, $offset, $limit, $flags = 0)
    {
        $queryIterator = $this->getQueryIterator($collection, $offset, $limit);

        for ($i = $offset, $max = $offset + $limit; $i < $max; ++$i) {
            $result = null;

            if ($collection->hasOverrideItem($i)) {
                $result = $this->buildOverrideResult($collection->getOverrideItem($i), $queryIterator);
            } elseif ($collection->hasManualItem($i)) {
                $result = $this->buildManualResult($collection->getManualItem($i), $queryIterator);
            } elseif ($queryIterator->valid()) {
                $result = new Result($i, $this->getQueryValue($queryIterator));
            }

            if (!$result instanceof Result) {
                return;
            }

            yield $result;
        }
    }

    public function count(Collection $collection)
    {
        $totalCount = $this->queryRunner->count($collection->getQuery());

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
     * Override items always cover the dynamic item, even when invisible or invalid.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     * @param \Iterator $queryIterator
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    private function buildOverrideResult(CollectionItem $collectionItem, Iterator $queryIterator)
    {
        $queryValue = $this->getQueryValue($queryIterator);

        if (!$collectionItem->isValid()) {
            if (!$queryValue instanceof CmsItem) {
                return null;
            }

            return new Result($collectionItem->getPosition(), $queryValue, new ManualItem($collectionItem));
        }

        return new Result($collectionItem->getPosition(), new ManualItem($collectionItem), $queryValue);
    }

    /**
     * Builds the result from a manual item.
     *
     * Manual items are replaced by dynamic ones when invisible or invalid.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     * @param \Iterator $queryIterator
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    private function buildManualResult(CollectionItem $collectionItem, Iterator $queryIterator)
    {
        if (!$collectionItem->isValid()) {
            $queryValue = $this->getQueryValue($queryIterator);
            if (!$queryValue instanceof CmsItem) {
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
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     *
     * @return \Iterator
     */
    private function getQueryIterator(Collection $collection, $offset, $limit)
    {
        $queryOffset = $offset - $this->getManualItemsCount($collection, 0, $offset);
        $queryLimit = $limit - $this->getManualItemsCount($collection, $offset, $offset + $limit);

        return call_user_func($this->queryRunner, $collection->getQuery(), $queryOffset, $queryLimit);
    }

    /**
     * Returns the count of valid manual items in a collection between $startOffset and $endOffset.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $startOffset
     * @param int $endOffset
     *
     * @return int
     */
    private function getManualItemsCount(Collection $collection, $startOffset, $endOffset)
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
