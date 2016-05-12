<?php

namespace Netgen\BlockManager\API\Values\Collection;

interface Item
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
    public function getId();

    /**
     * Returns the collection ID the item is in.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns the item position within the collection.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Returns the type of item in the collection.
     *
     * @return int
     */
    public function getType();

    /**
     * Returns the value ID.
     *
     * @return int|string
     */
    public function getValueId();

    /**
     * Returns the value type.
     *
     * @return string
     */
    public function getValueType();
}
