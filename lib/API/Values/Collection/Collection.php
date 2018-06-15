<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

interface Collection extends Value
{
    /**
     * Denotes that the collection is manual, i.e., does not have a collection.
     */
    const TYPE_MANUAL = 0;

    /**
     * Denotes that the collection is dynamic, i.e., that it has a collection.
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
    public function getType(): int;

    /**
     * Returns the starting collection offset.
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Returns the starting collection limit or null if no limit is set.
     *
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * Returns if the item with specified type (manual or override)
     * exists at specified position.
     *
     * @param int $position
     * @param int $type
     *
     * @return bool
     */
    public function hasItem(int $position, int $type = null): bool;

    /**
     * Returns the item of specified type (manual or override)
     * at specified position.
     *
     * @param int $position
     * @param int $type
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getItem(int $position, int $type = null): ?Item;

    /**
     * Returns all collection items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getItems(): array;

    /**
     * Returns if the collection has a manual item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasManualItem(int $position): bool;

    /**
     * Returns the manual item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getManualItem(int $position): ?Item;

    /**
     * Returns all the manual items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getManualItems(): array;

    /**
     * Returns if the collection has an override item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasOverrideItem(int $position): bool;

    /**
     * Returns the override item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getOverrideItem(int $position): ?Item;

    /**
     * Returns all the override items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getOverrideItems(): array;

    /**
     * Returns the query from the collection or null if no query exists.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query|null
     */
    public function getQuery(): ?Query;

    /**
     * Returns if the query exists in the collection.
     *
     * @return bool
     */
    public function hasQuery(): bool;

    /**
     * Returns the list of all available locales in the collection.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array;

    /**
     * Returns the main locale for the collection.
     *
     * @return string
     */
    public function getMainLocale(): string;

    /**
     * Returns if the collection is translatable.
     *
     * @return bool
     */
    public function isTranslatable(): bool;

    /**
     * Returns if the main translation of the collection will be used
     * in case there are no prioritized translations.
     *
     * @return bool
     */
    public function isAlwaysAvailable(): bool;

    /**
     * Returns the locale of the currently loaded translation.
     *
     * @return string
     */
    public function getLocale(): string;
}
