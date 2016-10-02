<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;

class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    protected $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemLoaderInterface $itemLoader
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     */
    public function __construct(ItemLoaderInterface $itemLoader, ItemBuilderInterface $itemBuilder)
    {
        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Builds the collection result from provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    public function buildResult(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $collectionIterator = new CollectionIterator($collection, $offset, $limit);

        $result = new Result(
            array(
                'collection' => $collection,
                'results' => $this->getResultItems($collectionIterator, $flags),
                'totalCount' => $collectionIterator->count(),
                'offset' => $offset,
                'limit' => $limit,
            )
        );

        return $result;
    }

    /**
     * Returns result items from collection items and query values.
     *
     * @param \Netgen\BlockManager\Collection\Result\CollectionIterator $collectionIterator
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\ResultItem[]
     */
    protected function getResultItems(CollectionIterator $collectionIterator, $flags = 0)
    {
        $resultItems = array();

        foreach ($collectionIterator as $position => $object) {
            $resultItem = $this->buildResultItem($object, $position);
            if ($this->isItemIncluded($resultItem->getItem(), $flags)) {
                $resultItems[] = $resultItem;
            }
        }

        return $resultItems;
    }

    /**
     * Builds the result item object.
     *
     * @param mixed $object
     * @param int $position
     *
     * @return \Netgen\BlockManager\Collection\Result\ResultItem
     */
    protected function buildResultItem($object, $position)
    {
        if ($object instanceof CollectionItem) {
            return new ResultItem(
                array(
                    'item' => $this->itemLoader->load($object->getValueId(), $object->getValueType()),
                    'collectionItem' => $object,
                    'type' => $object->getType() === CollectionItem::TYPE_MANUAL ?
                        ResultItem::TYPE_MANUAL :
                        ResultItem::TYPE_OVERRIDE,
                    'position' => $position,
                )
            );
        }

        return new ResultItem(
            array(
                'item' => $this->itemBuilder->build($object),
                'collectionItem' => null,
                'type' => ResultItem::TYPE_DYNAMIC,
                'position' => $position,
            )
        );
    }

    /**
     * Returns if current item will be included in result set.
     *
     * @TODO Refactor out to separate service
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     * @param $flags
     *
     * @return bool
     */
    protected function isItemIncluded(ItemInterface $item, $flags)
    {
        if (!$item instanceof NullItem) {
            if ((bool)($flags & self::INCLUDE_INVISIBLE_ITEMS)) {
                return true;
            }

            return $item->isVisible();
        }

        return (bool)($flags & self::INCLUDE_INVALID_ITEMS);
    }
}
