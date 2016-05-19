<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Value;

class ResultItem extends Value
{
    const TYPE_MANUAL = 1;

    const TYPE_OVERRIDE = 2;

    const TYPE_DYNAMIC = 3;

    /**
     * @var \Netgen\BlockManager\Collection\ResultValue
     */
    protected $value;

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
     * Returns the value.
     *
     * @return \Netgen\BlockManager\Collection\ResultValue
     */
    public function getValue()
    {
        return $this->value;
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
