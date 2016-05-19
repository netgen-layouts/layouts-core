<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\ResultItem;

class ResultItemBuilder implements ResultItemBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface
     */
    protected $resultValueBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface $resultValueBuilder
     */
    public function __construct(ResultValueBuilderInterface $resultValueBuilder)
    {
        $this->resultValueBuilder = $resultValueBuilder;
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
                'value' => $this->resultValueBuilder->build($object),
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
                'value' => $this->resultValueBuilder->buildFromItem($item),
                'collectionItem' => $item,
                'type' => $item->getType() === Item::TYPE_MANUAL ?
                    ResultItem::TYPE_MANUAL :
                    ResultItem::TYPE_DYNAMIC,
                'position' => $position,
            )
        );
    }
}
