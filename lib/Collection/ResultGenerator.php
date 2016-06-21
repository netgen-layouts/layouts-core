<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface;
use Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilderInterface;
use Exception;
use Netgen\BlockManager\Item\NullValue;

class ResultGenerator implements ResultGeneratorInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface
     */
    protected $queryRunner;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilderInterface
     */
    protected $resultItemBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface $queryRunner
     * @param \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilderInterface $resultItemBuilder
     */
    public function __construct(
        QueryRunnerInterface $queryRunner,
        ResultItemBuilderInterface $resultItemBuilder
    ) {
        $this->queryRunner = $queryRunner;
        $this->resultItemBuilder = $resultItemBuilder;
    }

    /**
     * Generates the collection result from provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result
     */
    public function generateResult(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $resultItems = $this->generateItems($collection, $offset, $limit, $flags);

        if (!($flags & self::INCLUDE_INVISIBLE_ITEMS)) {
            $resultItems = $this->filterInvisibleItems($resultItems);
        }

        $result = new Result(
            array(
                'collection' => $collection,
                'results' => $resultItems,
                'totalCount' => $this->getResultCount($collection, $flags),
                'offset' => $offset,
                'limit' => $limit,
            )
        );

        return $result;
    }

    /**
     * Builds the list of result items merged from the collection items
     * and the list of dynamic values retrieved from collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\ResultItem[]
     */
    protected function generateItems(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $manualItems = $collection->getManualItems();
        $overrideItems = $collection->getOverrideItems();
        $queryValues = array();

        $numberOfItemsBeforeOffset = $this->getNumberOfItemsBeforeOffset($manualItems, $offset);
        $numberOfItemsAtOffset = $this->getNumberOfItemsAtOffset($manualItems, $offset, $limit);

        try {
            $queryValues = $this->queryRunner->runQueries(
                $collection->getQueries(),
                $offset - $numberOfItemsBeforeOffset,
                $limit !== null ? $limit - $numberOfItemsAtOffset : null,
                (bool)($flags & self::INCLUDE_INVISIBLE_ITEMS)
            );
        } catch (Exception $e) {
            if (!($flags & self::IGNORE_EXCEPTIONS)) {
                throw $e;
            }
        }

        $resultItems = array();
        for ($i = $offset, $queryValuesIndex = 0;; ++$i) {
            if ($limit !== null && $i >= $offset + $limit) {
                break;
            }

            try {
                if (isset($overrideItems[$i])) {
                    $resultItem = $this->resultItemBuilder->buildFromItem($overrideItems[$i], $i);

                    // Since we're basically overriding the values that come
                    // from the outside of the collection (i.e. the queries),
                    // we need to advance the query pointer
                    ++$queryValuesIndex;
                } elseif (isset($manualItems[$i])) {
                    $resultItem = $this->resultItemBuilder->buildFromItem(
                        $manualItems[$i],
                        $i
                    );
                } elseif (isset($queryValues[$queryValuesIndex])) {
                    $resultItem = $this->resultItemBuilder->build(
                        $queryValues[$queryValuesIndex],
                        $i
                    );

                    ++$queryValuesIndex;
                } else {
                    // We don't want empty slots in final result.
                    break;
                }

                if (!$this->isItemIncluded($resultItem, $flags)) {
                    continue;
                }

                $resultItems[] = $resultItem;
            } catch (Exception $e) {
                if (!($flags & self::IGNORE_EXCEPTIONS)) {
                    throw $e;
                }

                continue;
            }
        }

        return $resultItems;
    }

    /**
     * Returns the total count of items in the result.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $flags
     *
     * @return int
     */
    protected function getResultCount(Collection $collection, $flags = 0)
    {
        $manualItems = $collection->getManualItems();
        $overrideItems = $collection->getOverrideItems();
        $queryCount = 0;

        try {
            $queryCount = $this->queryRunner->getTotalCount(
                $collection->getQueries(),
                (bool)($flags & self::INCLUDE_INVISIBLE_ITEMS)
            );
        } catch (Exception $e) {
            if (!($flags & self::IGNORE_EXCEPTIONS)) {
                throw $e;
            }
        }

        $totalCount = 0;

        for ($i = 0;; ++$i) {
            if (isset($overrideItems[$i])) {
                ++$totalCount;
                --$queryCount;
            } elseif (isset($manualItems[$i])) {
                ++$totalCount;
            } elseif ($queryCount > 0) {
                ++$totalCount;
                --$queryCount;
            } else {
                break;
            }
        }

        return $totalCount;
    }

    /**
     * Returns the count of manual items at positions before the original offset.
     *
     * Example: Original offset for fetching values from the queries is 10
     *          We already have 3 manual items injected at positions 3, 5, 9, 15 and 17
     *          Resulting count is 3
     *
     * @param array $manualItems
     * @param int $offset
     *
     * @return int
     */
    protected function getNumberOfItemsBeforeOffset(array $manualItems, $offset = 0)
    {
        return count(
            array_filter(
                array_keys($manualItems),
                function ($position) use ($offset) {
                    return $position < $offset;
                }
            )
        );
    }

    /**
     * Returns the count of manual items at positions between the original offset and (offset + limit - 1).
     *
     * Example: Original offset for fetching values from the queries is 10 and limit is also 10
     *          We already have 3 manual items injected at positions 3, 5, 9, 15 and 17
     *          Resulting count is 2
     *
     * @param array $manualItems
     * @param int $offset
     * @param int $limit
     *
     * @return int
     */
    protected function getNumberOfItemsAtOffset(array $manualItems, $offset = 0, $limit = null)
    {
        return count(
            array_filter(
                array_keys($manualItems),
                function ($position) use ($offset, $limit) {
                    if ($limit !== null) {
                        return $position >= $offset && $position < ($offset + $limit - 1);
                    }

                    return $position >= $offset;
                }
            )
        );
    }

    /**
     * Removes invisible items from the list.
     *
     * @param \Netgen\BlockManager\Collection\ResultItem[] $items
     *
     * @TODO Refactor out to separate service
     *
     * @return \Netgen\BlockManager\Collection\ResultItem[]
     */
    protected function filterInvisibleItems(array $items)
    {
        $visibleItems = array();
        foreach ($items as $item) {
            if ($item->getItem()->isVisible()) {
                $visibleItems[] = $item;
            }
        }

        return $visibleItems;
    }

    /**
     * Returns if current item will be included in result set.
     *
     * @param \Netgen\BlockManager\Collection\ResultItem $resultItem
     * @param $flags
     *
     * @return bool
     */
    protected function isItemIncluded(ResultItem $resultItem, $flags)
    {
        if (!$resultItem->getItem()->getObject() instanceof NullValue) {
            return true;
        }

        return (bool)($flags & self::INCLUDE_INVALID_ITEMS);
    }
}
