<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\ResultItem;

class ResultItemBuilder implements ResultItemBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     */
    public function __construct(ItemBuilderInterface $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Builds the result item from provided object.
     *
     * @param mixed $object
     * @param int $position
     *
     * @return \Netgen\BlockManager\Collection\ResultItem
     */
    public function build($object, $position)
    {
        return new ResultItem(
            array(
                'item' => $this->itemBuilder->buildFromObject($object),
                'collectionItem' => null,
                'type' => ResultItem::TYPE_DYNAMIC,
                'position' => $position,
            )
        );
    }

    /**
     * Builds the result item from provided collection item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param int $position
     *
     * @return \Netgen\BlockManager\Collection\ResultItem
     */
    public function buildFromItem(Item $item, $position)
    {
        return new ResultItem(
            array(
                'item' => $this->itemBuilder->build($item->getValueId(), $item->getValueType()),
                'collectionItem' => $item,
                'type' => $item->getType() === Item::TYPE_MANUAL ?
                    ResultItem::TYPE_MANUAL :
                    ResultItem::TYPE_OVERRIDE,
                'position' => $position,
            )
        );
    }
}
