<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface;
use Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface;
use Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface;
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
     * @var \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface $queryRunner
     * @param \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface $resultValueBuilder
     * @param \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     */
    public function __construct(
        QueryRunnerInterface $queryRunner,
        ResultValueBuilderInterface $resultValueBuilder,
        ValueLoaderRegistryInterface $valueLoaderRegistry
    ) {
        $this->queryRunner = $queryRunner;
        $this->resultValueBuilder = $resultValueBuilder;
        $this->valueLoaderRegistry = $valueLoaderRegistry;
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
            $values = $this->generateManualValues($collection, $offset, $limit);
        } else {
            $values = $this->generateDynamicValues($collection, $offset, $limit);
        }

        $result = new Result();
        $result->collection = $collection;
        $result->values = $this->filterInvisibleValues($values);
        $result->offset = $offset;
        $result->limit = $limit;

        return $result;
    }

    /**
     * Builds the list of values from a manual collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Collection\ResultValue[]
     */
    protected function generateManualValues(Collection $collection, $offset = 0, $limit = null)
    {
        $items = array_slice($collection->getItems(), $offset, $limit);

        $resultValues = array();
        foreach ($items as $item) {
            $resultValues[] = $this->getValueFromItem($item);
        }

        return $resultValues;
    }

    /**
     * Builds the list of values merged from the collection items
     * and the list of dynamic values retrieved from collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     *
     * @throws \RuntimeException If collection has no queries.
     *
     * @return \Netgen\BlockManager\Collection\ResultValue[]
     */
    protected function generateDynamicValues(Collection $collection, $offset = 0, $limit = null)
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

        $resultValues = array();
        for ($i = $offset, $queryValuesIndex = 0; $i < $offset + count($queryValues) + $numberOfItemsAtOffset; ++$i) {
            if (isset($overrideItems[$i])) {
                $resultValues[] = $this->getValueFromItem($overrideItems[$i]);

                // Since we're basically overriding the values that come
                // from the outside of the collection (i.e. the queries),
                // we need to advance the query pointer
                ++$queryValuesIndex;
            } elseif (isset($manualItems[$i])) {
                $resultValues[] = $this->getValueFromItem($manualItems[$i]);
            } elseif (isset($queryValues[$queryValuesIndex])) {
                $resultValues[] = $this->resultValueBuilder->build($queryValues[$queryValuesIndex]);
                ++$queryValuesIndex;
            } else {
                // We don't want empty slots in final result.
                break;
            }
        }

        return $resultValues;
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
     * Loads and builds the value from provided item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\Collection\ResultValue
     */
    protected function getValueFromItem(Item $item)
    {
        $loadedValue = $this->valueLoaderRegistry
            ->getValueLoader($item->getValueType())
            ->load($item->getValueId());

        return $this->resultValueBuilder->build($loadedValue);
    }

    /**
     * Removes invisible values from the list.
     *
     * @param \Netgen\BlockManager\Collection\ResultValue[] $values
     *
     * @TODO Refactor out to separate service
     *
     * @return \Netgen\BlockManager\Collection\ResultValue[]
     */
    protected function filterInvisibleValues(array $values)
    {
        $visibleValues = array();
        foreach ($values as $value) {
            if ($value->isVisible) {
                $visibleValues[] = $value;
            }
        }

        return $visibleValues;
    }
}
