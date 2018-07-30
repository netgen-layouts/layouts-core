<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

interface Collection extends Value
{
    /**
     * Denotes that the collection is manual, i.e., does not have a query.
     */
    public const TYPE_MANUAL = 0;

    /**
     * Denotes that the collection is dynamic, i.e., that it has a query.
     */
    public const TYPE_DYNAMIC = 1;

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the starting collection offset.
     */
    public function getOffset(): int;

    /**
     * Returns the starting collection limit or null if no limit is set.
     */
    public function getLimit(): ?int;

    /**
     * Returns if the item exists at specified position.
     */
    public function hasItem(int $position): bool;

    /**
     * Returns the item at specified position.
     */
    public function getItem(int $position): ?Item;

    /**
     * Returns all collection items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getItems(): array;

    /**
     * Returns the query from the collection or null if no query exists.
     */
    public function getQuery(): ?Query;

    /**
     * Returns if the query exists in the collection.
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
     */
    public function getMainLocale(): string;

    /**
     * Returns if the collection is translatable.
     */
    public function isTranslatable(): bool;

    /**
     * Returns if the main translation of the collection will be used
     * in case there are no prioritized translations.
     */
    public function isAlwaysAvailable(): bool;

    /**
     * Returns the locale of the currently loaded translation.
     */
    public function getLocale(): string;
}
