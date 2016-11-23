<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use IteratorIterator;
use Iterator;

class ResultBuilderIterator extends IteratorIterator
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
     * Returns the result for provided object.
     *
     * Object can be a collection item, which only holds the reference
     * to the value (ID and type), or a value itself.
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    public function current()
    {
        $object = parent::current();
        $position = parent::key();

        if (!$object instanceof Item) {
            return new Result(
                array(
                    'item' => $this->itemBuilder->build($object),
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'position' => $position,
                )
            );
        }

        try {
            $item = $this->itemLoader->load(
                $object->getValueId(),
                $object->getValueType()
            );
        } catch (InvalidItemException $e) {
            $item = new NullItem($object->getValueId());
        }

        return new Result(
            array(
                'item' => $item,
                'collectionItem' => $object,
                'type' => Result::TYPE_MANUAL,
                'position' => $position,
            )
        );
    }
}
