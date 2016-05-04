<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

abstract class Item extends Value
{
    /**
     * @const int
     */
    const TYPE_MANUAL = 0;

    /**
     * @const int
     */
    const TYPE_OVERRIDE = 1;

    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    abstract public function getId();

    /**
     * Returns the item status.
     *
     * @return int
     */
    abstract public function getStatus();

    /**
     * Returns the collection ID the item is in.
     *
     * @return int|string
     */
    abstract public function getCollectionId();

    /**
     * Returns the item position within the collection.
     *
     * @return int
     */
    abstract public function getPosition();

    /**
     * Returns the type of item in the collection.
     *
     * @return int
     */
    abstract public function getType();

    /**
     * Returns the value ID.
     *
     * @return int|string
     */
    abstract public function getValueId();

    /**
     * Returns the value type.
     *
     * @return string
     */
    abstract public function getValueType();
}
