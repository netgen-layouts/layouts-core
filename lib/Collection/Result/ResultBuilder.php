<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Item\NullItem;

/**
 * A builder that uses iterators to generate the collection results. Note that in order to disable
 * fetching unlimited number of results (for performance reasons), the number of results is hardcoded
 * to a max limit provided in the constructor.
 */
final class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory
     */
    private $collectionIteratorFactory;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultItemBuilder
     */
    private $resultItemBuilder;

    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    /**
     * @var int
     */
    private $maxLimit;

    /**
     * @param \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory $collectionIteratorFactory
     * @param \Netgen\BlockManager\Collection\Result\ResultItemBuilder $resultItemBuilder
     * @param \Netgen\BlockManager\Collection\Item\VisibilityResolverInterface $visibilityResolver
     * @param int $maxLimit
     */
    public function __construct(
        CollectionIteratorFactory $collectionIteratorFactory,
        ResultItemBuilder $resultItemBuilder,
        VisibilityResolverInterface $visibilityResolver,
        $maxLimit
    ) {
        $this->collectionIteratorFactory = $collectionIteratorFactory;
        $this->resultItemBuilder = $resultItemBuilder;
        $this->visibilityResolver = $visibilityResolver;
        $this->maxLimit = $maxLimit;
    }

    public function build(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $offset = $offset >= 0 ? $offset : 0;
        if ($limit === null || $limit < 0 || $limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        }

        $collectionIterator = $this->collectionIteratorFactory->getCollectionIterator(
            $collection,
            $offset,
            $limit,
            $flags
        );

        $results = array();

        $collectionCount = $collectionIterator->count();
        if ($limit > 0 && $offset < $collectionCount) {
            $results = $this->getResults($collectionIterator, $flags);
        }

        $overflowResults = array();

        if ((bool) ($flags & ResultSet::INCLUDE_OVERFLOW_ITEMS)) {
            $overflowResults = $this->getOverflowResults($collection, $results);
        }

        return new ResultSet(
            array(
                'collection' => $collection,
                'results' => array_values($results),
                'overflowResults' => $overflowResults,
                'totalCount' => $collectionCount,
                'offset' => $offset,
                'limit' => $limit,
            )
        );
    }

    /**
     * Returns all items from the collection which are included in the result.
     * Those are the items that fit inside the offset and limit, as defined by
     * the collection.
     *
     * @param \Netgen\BlockManager\Collection\Result\CollectionIterator $collectionIterator
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\Result[]
     */
    private function getResults(CollectionIterator $collectionIterator, $flags = 0)
    {
        $results = array();

        foreach ($collectionIterator as $position => $item) {
            $result = $this->resultItemBuilder->build($item, $position);

            if (!$this->includeResult($result, $flags)) {
                continue;
            }

            $results[$position] = $result;
        }

        return $results;
    }

    /**
     * Returns if the provided result should be included in the result set. Result is included
     * in the set if it is valid and visible, or if its inclusion is overriden by the provided
     * flags (specifying if invalid or invisible items should be included or not.).
     *
     * @param \Netgen\BlockManager\Collection\Result\Result $result
     * @param int $flags
     *
     * @return bool
     */
    private function includeResult(Result $result, $flags = 0)
    {
        if (!((bool) ($flags & ResultSet::INCLUDE_INVALID_ITEMS)) && $result->getItem() instanceof NullItem) {
            return false;
        }

        if (!((bool) ($flags & ResultSet::INCLUDE_INVISIBLE_ITEMS)) && !$this->isResultVisible($result)) {
            return false;
        }

        return true;
    }

    /**
     * Returns if the result built from CMS item and collection item will be
     * visible or not.
     *
     * First, we check if the item from CMS is visible or not. After that, we
     * check if the collection item is specified as visible by its configuration,
     * and finally, we use the visibility resolver to give the change to code
     * to specify if an item is visible or not.
     *
     * @param \Netgen\BlockManager\Collection\Result\Result $result
     *
     * @return bool
     */
    private function isResultVisible(Result $result)
    {
        if (!$result->getItem()->isVisible()) {
            return false;
        }

        $collectionItem = $result->getCollectionItem();
        if (!$collectionItem instanceof Item) {
            return true;
        }

        if (!$collectionItem->isVisible()) {
            return false;
        }

        return $this->visibilityResolver->isVisible($collectionItem);
    }

    /**
     * Returns all items from the collection which are overflown. Overflown items
     * are those NOT included in the provided results, as defined by collection
     * offset and limit.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Collection\Result\Result[] $results
     *
     * @return \Netgen\BlockManager\Collection\Result\Result[]
     */
    private function getOverflowResults(Collection $collection, array $results)
    {
        $overflowResults = array();

        foreach ($collection->getItems() as $item) {
            if (array_key_exists($item->getPosition(), $results)) {
                continue;
            }

            $overflowResults[] = $this->resultItemBuilder->build($item, $item->getPosition());
        }

        return $overflowResults;
    }
}
