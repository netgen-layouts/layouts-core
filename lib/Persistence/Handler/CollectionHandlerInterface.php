<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryTranslationUpdateStruct;

interface CollectionHandlerInterface
{
    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function loadCollection($collectionId, int $status): Collection;

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function loadItem($itemId, int $status): Item;

    /**
     * Loads an item with specified position in specified collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function loadItemWithPosition(Collection $collection, int $position): Item;

    /**
     * Loads all items that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function loadCollectionItems(Collection $collection): array;

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function loadQuery($queryId, int $status): Query;

    /**
     * Loads the query that belongs to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query for specified collection does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function loadCollectionQuery(Collection $collection): Query;

    /**
     * Returns if collection with specified ID exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, int $status): bool;

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct $collectionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct): Collection;

    /**
     * Creates a collection translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $locale
     * @param string $sourceLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If translation with provided locale already exists
     *                                                          If translation with provided source locale does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollectionTranslation(Collection $collection, string $locale, string $sourceLocale): Collection;

    /**
     * Updates the main translation of the collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $mainLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided locale does not exist in the collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function setMainTranslation(Collection $collection, string $mainLocale): Collection;

    /**
     * Updates a collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct): Collection;

    /**
     * Copies a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection): Collection;

    /**
     * Creates a new collection status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollectionStatus(Collection $collection, int $newStatus): Collection;

    /**
     * Deletes a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollection($collectionId, int $status = null): void;

    /**
     * Deletes provided collection translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If translation with provided locale does not exist
     *                                                          If translation with provided locale is the main collection translation
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function deleteCollectionTranslation(Collection $collection, string $locale): Collection;

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct $itemCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct): Item;

    /**
     * Updates an item with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     * @param \Netgen\BlockManager\Persistence\Values\Collection\ItemUpdateStruct $itemUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function updateItem(Item $item, ItemUpdateStruct $itemUpdateStruct): Item;

    /**
     * Moves an item to specified position in the collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function moveItem(Item $item, int $position): Item;

    /**
     * Switch item positions within the same collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item1
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item2
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If items are the same
     *                                                          If items are not within the same collection
     */
    public function switchItemPositions(Item $item1, Item $item2): void;

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     */
    public function deleteItem(Item $item): void;

    /**
     * Removes all manual and override items from provided collection.
     *
     * If item type (one of Item::TYPE_* constants) is provided, only items
     * of that type are removed (manual or override).
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $itemType
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function deleteItems(Collection $collection, int $itemType = null): Collection;

    /**
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection already has a query
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function createQuery(Collection $collection, QueryCreateStruct $queryCreateStruct): Query;

    /**
     * Updates a query translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param string $locale
     * @param \Netgen\BlockManager\Persistence\Values\Collection\QueryTranslationUpdateStruct $translationUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If the query does not have the provided locale
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQueryTranslation(Query $query, string $locale, QueryTranslationUpdateStruct $translationUpdateStruct): Query;

    /**
     * Removes a query from the collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     */
    public function deleteCollectionQuery(Collection $collection): void;
}
