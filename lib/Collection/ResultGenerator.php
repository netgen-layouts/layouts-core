<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface;
use Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface;
use RuntimeException;

class ResultGenerator implements ResultGeneratorInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface
     */
    protected $queryRunner;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface
     */
    protected $resultValueBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface $queryRunner
     * @param \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface $resultValueBuilder
     */
    public function __construct(
        QueryRunnerInterface $queryRunner,
        ResultValueBuilderInterface $resultValueBuilder
    ) {
        $this->queryRunner = $queryRunner;
        $this->resultValueBuilder = $resultValueBuilder;
    }

    /**
     * Generates the collection result from provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Collection\Result
     */
    public function generateResult(Collection $collection, $offset = 0, $limit = null)
    {
        if ($collection->getType() === Collection::TYPE_MANUAL) {
            $resultItems = $this->generateFromManualCollection($collection, $offset, $limit);
        } else {
            $resultItems = $this->generateFromDynamicCollection($collection, $offset, $limit);
        }

        $result = new Result(
            array(
                'collection' => $collection,
                'items' => $this->filterInvisibleItems($resultItems),
                'offset' => $offset,
                'limit' => $limit,
            )
        );

        return $result;
    }

    /**
     * Builds the list of result items from a manual collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Collection\ResultItem[]
     */
    protected function generateFromManualCollection(Collection $collection, $offset = 0, $limit = null)
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Item[] $items */
        $items = array_slice($collection->getManualItems(), $offset, $limit);

        $resultItems = array();
        foreach ($items as $item) {
            $resultItems[] = new ResultItem(
                array(
                    'value' => $this->resultValueBuilder->buildFromItem($item),
                    'collectionItem' => $item,
                    'type' => ResultItem::TYPE_MANUAL,
                    'position' => $item->getPosition(),
                )
            );
        }

        return $resultItems;
    }

    /**
     * Builds the list of result items merged from the collection items
     * and the list of dynamic values retrieved from collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     *
     * @throws \RuntimeException If collection has no queries.
     *
     * @return \Netgen\BlockManager\Collection\ResultItem[]
     */
    protected function generateFromDynamicCollection(Collection $collection, $offset = 0, $limit = null)
    {
        $collectionQueries = $collection->getQueries();
        if (empty($collectionQueries)) {
            throw new RuntimeException('Collection has no queries.');
        }

        $manualItems = $collection->getManualItems();
        $overrideItems = $collection->getOverrideItems();

        $numberOfItemsBeforeOffset = $this->getNumberOfItemsBeforeOffset($manualItems, $offset);
        $numberOfItemsAtOffset = $this->getNumberOfItemsAtOffset($manualItems, $offset, $limit);

        $queryValues = $this->queryRunner->runQueries(
            $collectionQueries,
            $offset - $numberOfItemsBeforeOffset,
            $limit !== null ? $limit - $numberOfItemsAtOffset : null
        );

        $resultItems = array();
        for ($i = $offset, $queryValuesIndex = 0; $i < $offset + count($queryValues) + $numberOfItemsAtOffset; ++$i) {
            if (isset($overrideItems[$i])) {
                $resultItem = new ResultItem(
                    array(
                        'value' => $this->resultValueBuilder->buildFromItem($overrideItems[$i]),
                        'collectionItem' => $overrideItems[$i],
                        'type' => ResultItem::TYPE_OVERRIDE,
                        'position' => $i,
                    )
                );

                // Since we're basically overriding the values that come
                // from the outside of the collection (i.e. the queries),
                // we need to advance the query pointer
                ++$queryValuesIndex;
            } elseif (isset($manualItems[$i])) {
                $resultItem = new ResultItem(
                    array(
                        'value' => $this->resultValueBuilder->buildFromItem($manualItems[$i]),
                        'collectionItem' => $manualItems[$i],
                        'type' => ResultItem::TYPE_MANUAL,
                        'position' => $i,
                    )
                );
            } elseif (isset($queryValues[$queryValuesIndex])) {
                $resultItem = new ResultItem(
                    array(
                        'value' => $this->resultValueBuilder->build($queryValues[$queryValuesIndex]),
                        'collectionItem' => null,
                        'type' => ResultItem::TYPE_DYNAMIC,
                        'position' => $i,
                    )
                );

                ++$queryValuesIndex;
            } else {
                // We don't want empty slots in final result.
                break;
            }

            $resultItems[] = $resultItem;
        }

        return $resultItems;
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
            if ($item->getValue()->isVisible()) {
                $visibleItems[] = $item;
            }
        }

        return $visibleItems;
    }
}
