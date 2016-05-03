<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

abstract class Collection extends Value
{
    /**
     * @const int
     */
    const TYPE_MANUAL = 0;

    /**
     * @const int
     */
    const TYPE_DYNAMIC = 1;

    /**
     * @const int
     */
    const TYPE_NAMED = 2;

    /**
     * @const int
     */
    const STATUS_DRAFT = 0;

    /**
     * @const int
     */
    const STATUS_PUBLISHED = 1;

    /**
     * @const int
     */
    const STATUS_ARCHIVED = 2;

    /**
     * @const int
     */
    const STATUS_TEMPORARY_DRAFT = 3;

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    abstract public function getId();

    /**
     * Returns the collection status.
     *
     * @return int
     */
    abstract public function getStatus();

    /**
     * Returns the collection type.
     *
     * @return int
     */
    abstract public function getType();

    /**
     * Returns the collection name.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Returns the list of items manually added to the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    abstract public function getManualItems();

    /**
     * Returns the list of items that override the item present at a specific slot.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    abstract public function getOverrideItems();

    /**
     * Returns the list of query configurations in the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query[]
     */
    abstract public function getQueries();
}
