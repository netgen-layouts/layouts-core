<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use IteratorIterator;
use Iterator;
use Netgen\BlockManager\Item\NullItem;

class ItemLoaderIterator extends IteratorIterator
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
     * @param \Iterator $iterator
     * @param \Netgen\BlockManager\Item\ItemLoaderInterface $itemLoader
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     */
    public function __construct(
        Iterator $iterator,
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder
    ) {
        parent::__construct($iterator);

        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Returns the item for provided object.
     *
     * Object can be a collection item, which only holds the reference to the value (ID and type),
     * or a value itself.
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function current()
    {
        $object = parent::current();

        if (!$object instanceof Item) {
            return $this->itemBuilder->build($object);
        }

        try {
            $item = $this->itemLoader->load(
                $object->getValueId(),
                $object->getValueType()
            );
        } catch (InvalidItemException $e) {
            $item = new NullItem($object->getValueId());
        }

        return new CollectionItemBased($item, $object);
    }
}
