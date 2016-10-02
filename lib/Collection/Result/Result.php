<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\ValueObject;

class Result extends ValueObject
{
    const TYPE_MANUAL = 0;

    const TYPE_OVERRIDE = 1;

    const TYPE_DYNAMIC = 2;

    /**
     * @var \Netgen\BlockManager\Item\ItemInterface
     */
    protected $item;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item
     */
    protected $collectionItem;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $position;

    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Returns the collection item.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getCollectionItem()
    {
        return $this->collectionItem;
    }

    /**
     * Returns the type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
