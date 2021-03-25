<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Handler;

use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\QueryTranslationUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Slot;
use Netgen\Layouts\Persistence\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\SlotUpdateStruct;

interface CollectionHandlerInterface
{
    /**
     * Loads a collection with specified ID.
     *
     * Collection ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $collectionId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If collection with specified ID does not exist
     */
    public function loadCollection($collectionId, int $status): Collection;

    /**
     * Loads all collections belonging to the provided block.
     *
     * @return array<string, \Netgen\Layouts\Persistence\Values\Collection\Collection>
     */
    public function loadCollections(Block $block): array;

    /**
     * Loads a collection reference.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If collection reference with specified identifier does not exist
     */
    public function loadCollectionReference(Block $block, string $identifier): CollectionReference;

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @return \Netgen\Layouts\Persistence\Values\Block\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block): array;

    /**
     * Loads an item with specified ID.
     *
     * Item ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $itemId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If item with specified ID does not exist
     */
    public function loadItem($itemId, int $status): Item;

    /**
     * Loads an item with specified position in specified collection.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If item does not exist
     */
    public function loadItemWithPosition(Collection $collection, int $position): Item;

    /**
     * Loads all items that belong to specified collection.
     *
     * @return \Netgen\Layouts\Persistence\Values\Collection\Item[]
     */
    public function loadCollectionItems(Collection $collection): array;

    /**
     * Loads a query with specified ID.
     *
     * Query ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $queryId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If query with specified ID does not exist
     */
    public function loadQuery($queryId, int $status): Query;

    /**
     * Loads the query that belongs to collection with specified ID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If query for specified collection does not exist
     */
    public function loadCollectionQuery(Collection $collection): Query;

    /**
     * Loads a slot with specified ID.
     *
     * Slot ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $slotId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If slot with specified ID does not exist
     */
    public function loadSlot($slotId, int $status): Slot;

    /**
     * Loads the slots that belong to specified collection.
     *
     * @return \Netgen\Layouts\Persistence\Values\Collection\Slot[]
     */
    public function loadCollectionSlots(Collection $collection): array;

    /**
     * Returns if collection with specified ID exists.
     *
     * Collection ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $collectionId
     */
    public function collectionExists($collectionId, int $status): bool;

    /**
     * Creates a collection in the specified block.
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct, Block $block, string $collectionIdentifier): Collection;

    /**
     * Creates a collection translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If translation with provided locale already exists
     *                                                     If translation with provided source locale does not exist
     */
    public function createCollectionTranslation(Collection $collection, string $locale, string $sourceLocale): Collection;

    /**
     * Adds the provided collection to the block and assigns it the specified identifier.
     */
    public function createCollectionReference(Collection $collection, Block $block, string $collectionIdentifier): CollectionReference;

    /**
     * Updates the main translation of the collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided locale does not exist in the collection
     */
    public function setMainTranslation(Collection $collection, string $mainLocale): Collection;

    /**
     * Updates a collection with specified ID.
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct): Collection;

    /**
     * Copies a collection to a specified block.
     */
    public function copyCollection(Collection $collection, Block $block, string $collectionIdentifier): Collection;

    /**
     * Creates a new collection status.
     */
    public function createCollectionStatus(Collection $collection, int $newStatus): Collection;

    /**
     * Deletes a collection with specified ID.
     */
    public function deleteCollection(int $collectionId, ?int $status = null): void;

    /**
     * Deletes block collections with specified block IDs.
     *
     * @param int[] $blockIds
     */
    public function deleteBlockCollections(array $blockIds, ?int $status = null): void;

    /**
     * Deletes provided collection translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If translation with provided locale does not exist
     *                                                     If translation with provided locale is the main collection translation
     */
    public function deleteCollectionTranslation(Collection $collection, string $locale): Collection;

    /**
     * Adds an item to collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided position is out of range (for manual collections)
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct): Item;

    /**
     * Updates an item with specified ID.
     */
    public function updateItem(Item $item, ItemUpdateStruct $itemUpdateStruct): Item;

    /**
     * Moves an item to specified position in the collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided position is out of range (for manual collections)
     */
    public function moveItem(Item $item, int $position): Item;

    /**
     * Switch item positions within the same collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If items are the same
     *                                                     If items are not within the same collection
     */
    public function switchItemPositions(Item $item1, Item $item2): void;

    /**
     * Removes an item.
     */
    public function deleteItem(Item $item): void;

    /**
     * Removes all items from provided collection.
     */
    public function deleteItems(Collection $collection): Collection;

    /**
     * Returns if the slot with specified position exists in the collection.
     */
    public function slotWithPositionExists(Collection $collection, int $position): bool;

    /**
     * Adds a slot to collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If slot with provided position already exists
     */
    public function addSlot(Collection $collection, SlotCreateStruct $slotCreateStruct): Slot;

    /**
     * Updates a slot with specified ID.
     */
    public function updateSlot(Slot $slot, SlotUpdateStruct $slotUpdateStruct): Slot;

    /**
     * Removes a slot.
     */
    public function deleteSlot(Slot $slot): void;

    /**
     * Removes all slots from provided collection.
     */
    public function deleteSlots(Collection $collection): Collection;

    /**
     * Adds a query to collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If collection already has a query
     */
    public function createQuery(Collection $collection, QueryCreateStruct $queryCreateStruct): Query;

    /**
     * Updates a query translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If the query does not have the provided locale
     */
    public function updateQueryTranslation(Query $query, string $locale, QueryTranslationUpdateStruct $translationUpdateStruct): Query;

    /**
     * Removes a query from the collection.
     */
    public function deleteCollectionQuery(Collection $collection): void;
}
