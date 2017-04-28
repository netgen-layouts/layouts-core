<?php

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use IteratorIterator;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;

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
                    'item' => !$object instanceof ItemInterface ?
                        $this->itemBuilder->build($object) :
                        $object,
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
        } catch (ItemException $e) {
            $item = new NullItem(
                array(
                    'valueId' => $object->getValueId(),
                )
            );
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
