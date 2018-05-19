<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

interface Collection extends Value
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
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the collection type.
     *
     * The type can be manual (can have only manual items)
     * or dynamic (can have manual items as well as a query).
     *
     * @return int
     */
    public function getType();

    /**
     * Returns the starting collection offset.
     *
     * @return int
     */
    public function getOffset();

    /**
     * Returns the starting collection limit or null if no limit is set.
     *
     * @return int|null
     */
    public function getLimit();

    /**
     * Returns if the item with specified type (manual or override)
     * exists at specified position.
     *
     * @param int $position
     * @param int $type
     *
     * @return bool
     */
    public function hasItem($position, $type = null);

    /**
     * Returns the item of specified type (manual or override)
     * at specified position.
     *
     * @param int $position
     * @param int $type
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getItem($position, $type = null);

    /**
     * Returns all collection items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getItems();

    /**
     * Returns if the collection has a manual item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasManualItem($position);

    /**
     * Returns the manual item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getManualItem($position);

    /**
     * Returns all the manual items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getManualItems();

    /**
     * Returns if the collection has an override item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasOverrideItem($position);

    /**
     * Returns the override item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getOverrideItem($position);

    /**
     * Returns all the override items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getOverrideItems();

    /**
     * Returns the query from the collection or null if no query exists.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query|null
     */
    public function getQuery();

    /**
     * Returns if the query exists in the collection.
     *
     * @return bool
     */
    public function hasQuery();

    /**
     * Returns the list of all available locales in the collection.
     *
     * @return string[]
     */
    public function getAvailableLocales();

    /**
     * Returns the main locale for the collection.
     *
     * @return string
     */
    public function getMainLocale();

    /**
     * Returns if the collection is translatable.
     *
     * @return bool
     */
    public function isTranslatable();

    /**
     * Returns if the main translation of the collection will be used
     * in case there are no prioritized translations.
     *
     * @return bool
     */
    public function isAlwaysAvailable();

    /**
     * Returns the locale of the currently loaded translation.
     *
     * @return string
     */
    public function getLocale();
}
