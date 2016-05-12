<?php

namespace Netgen\BlockManager\API\Values\Collection;

interface Collection
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
    public function getId();

    /**
     * Returns the collection status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns the collection type.
     *
     * @return int
     */
    public function getType();

    /**
     * Returns the collection name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns all collection items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getItems();

    /**
     * Returns the list of items manually added to the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getManualItems();

    /**
     * Returns the list of items that override the item present at a specific slot.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getOverrideItems();

    /**
     * Returns the list of query configurations in the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query[]
     */
    public function getQueries();
}
