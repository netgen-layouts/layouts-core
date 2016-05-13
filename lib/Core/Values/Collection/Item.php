<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Value;

class Item extends Value implements APIItem
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int|string
     */
    protected $collectionId;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int|string
     */
    protected $valueId;

    /**
     * @var string
     */
    protected $valueType;

    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the item status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the collection ID the item is in.
     *
     * @return int|string
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Returns the item position within the collection.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Returns the type of item in the collection.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the value ID.
     *
     * @return int|string
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * Returns the value type.
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }
}
