<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use ArrayIterator;

class ResultLoader implements ResultLoaderInterface
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
     * @var \Netgen\BlockManager\Collection\Result\ResultIteratorFactory
     */
    protected $resultIteratorFactory;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemLoaderInterface $itemLoader
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     * @param \Netgen\BlockManager\Collection\Result\ResultIteratorFactory $resultIteratorFactory
     */
    public function __construct(
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder,
        ResultIteratorFactory $resultIteratorFactory
    ) {
        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
        $this->resultIteratorFactory = $resultIteratorFactory;
    }

    /**
     * Loads the result set for provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\ResultSet
     */
    public function load(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $collectionIterator = new CollectionIterator($collection, $offset, $limit);

        $items = array();
        foreach ($collectionIterator as $object) {
            $items[] = $this->loadItem($object);
        }

        $results = $this->resultIteratorFactory->getResultIterator(
            new ArrayIterator($items),
            $flags
        );

        return new ResultSet(
            array(
                'collection' => $collection,
                'results' => iterator_to_array($results),
                'totalCount' => $collectionIterator->count(),
                'offset' => $offset,
                'limit' => $limit,
            )
        );
    }

    /**
     * Returns the item for provided object.
     *
     * Object can be a collection item, which only holds the reference to the value (ID and type),
     * or a value itself.
     *
     * @param mixed $object
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    protected function loadItem($object)
    {
        if (!$object instanceof Item) {
            return $this->itemBuilder->build($object);
        }

        $item = $this->itemLoader->load(
            $object->getValueId(),
            $object->getValueType()
        );

        return new CollectionItem($item, $object);
    }
}
