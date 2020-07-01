<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Service;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\API\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\API\Values\Collection\SlotUpdateStruct;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Ramsey\Uuid\UuidInterface;

interface CollectionService extends TransactionService
{
    /**
     * Loads a collection with specified UUID.
     *
     * By default, collection is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the collection is always available and $useMainLocale is
     * set to true, collection in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If collection with specified UUID does not exist
     */
    public function loadCollection(UuidInterface $collectionId, ?array $locales = null, bool $useMainLocale = true): Collection;

    /**
     * Loads a collection draft with specified UUID.
     *
     * By default, collection is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the collection is always available and $useMainLocale is
     * set to true, collection in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If collection with specified UUID does not exist
     */
    public function loadCollectionDraft(UuidInterface $collectionId, ?array $locales = null, bool $useMainLocale = true): Collection;

    /**
     * Updates a collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If collection is not a draft
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct): Collection;

    /**
     * Loads an item with specified UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If item with specified UUID does not exist
     */
    public function loadItem(UuidInterface $itemId): Item;

    /**
     * Loads an item draft with specified UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If item with specified UUID does not exist
     */
    public function loadItemDraft(UuidInterface $itemId): Item;

    /**
     * Loads a query with specified UUID.
     *
     * By default, query is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the query is always available and $useMainLocale is
     * set to true, query in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If query with specified UUID does not exist
     */
    public function loadQuery(UuidInterface $queryId, ?array $locales = null, bool $useMainLocale = true): Query;

    /**
     * Loads a query with specified UUID.
     *
     * By default, query is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the query is always available and $useMainLocale is
     * set to true, query in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If query with specified UUID does not exist
     */
    public function loadQueryDraft(UuidInterface $queryId, ?array $locales = null, bool $useMainLocale = true): Query;

    /**
     * Loads a slot with specified UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If slot with specified UUID does not exist
     */
    public function loadSlot(UuidInterface $slotId): Slot;

    /**
     * Loads a slot draft with specified UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If slot with specified UUID does not exist
     */
    public function loadSlotDraft(UuidInterface $slotId): Slot;

    /**
     * Changes the type of specified collection.
     *
     * If new type is a dynamic collection, you also need to provide the QueryCreateStruct used to
     * create the query in the collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If collection is not a draft
     *                                                     If collection type cannot be changed
     */
    public function changeCollectionType(Collection $collection, int $newType, ?QueryCreateStruct $queryCreateStruct = null): Collection;

    /**
     * Adds an item to collection at specified position.
     *
     * If position is not provided, item is placed at the end of the collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If collection is not a draft
     *                                                     If position is out of range (for manual collections)
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct, ?int $position = null): Item;

    /**
     * Updates a specified item.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If item is not a draft
     */
    public function updateItem(Item $item, ItemUpdateStruct $itemUpdateStruct): Item;

    /**
     * Moves an item within the collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If item is not a draft
     *                                                     If position is out of range (for manual collections)
     */
    public function moveItem(Item $item, int $position): Item;

    /**
     * Removes an item.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If item is not a draft
     */
    public function deleteItem(Item $item): void;

    /**
     * Removes all items from provided collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If collection is not a draft
     */
    public function deleteItems(Collection $collection): Collection;

    /**
     * Updates a query.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If query is not a draft
     *                                                     If query does not have a specified translation
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct): Query;

    /**
     * Adds a slot to collection to a provided position.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If collection is not a draft
     *                                                     If slot with provided position already exists in the collection
     */
    public function addSlot(Collection $collection, SlotCreateStruct $slotCreateStruct, int $position): Slot;

    /**
     * Updates a specified slot.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If slot is not a draft
     */
    public function updateSlot(Slot $slot, SlotUpdateStruct $slotUpdateStruct): Slot;

    /**
     * Removes an slot.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If slot is not a draft
     */
    public function deleteSlot(Slot $slot): void;

    /**
     * Removes all slots from provided collection.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If collection is not a draft
     */
    public function deleteSlots(Collection $collection): Collection;

    /**
     * Creates a new collection create struct.
     */
    public function newCollectionCreateStruct(?QueryCreateStruct $queryCreateStruct = null): CollectionCreateStruct;

    /**
     * Creates a new collection update struct.
     *
     * If collection is provided, initial data is copied from the collection.
     */
    public function newCollectionUpdateStruct(?Collection $collection = null): CollectionUpdateStruct;

    /**
     * Creates a new item create struct from provided values.
     *
     * @param int|string $value
     */
    public function newItemCreateStruct(ItemDefinitionInterface $itemDefinition, $value): ItemCreateStruct;

    /**
     * Creates a new item update struct.
     *
     * If item is provided, initial data is copied from the item.
     */
    public function newItemUpdateStruct(?Item $item = null): ItemUpdateStruct;

    /**
     * Creates a new query create struct from provided query type.
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType): QueryCreateStruct;

    /**
     * Creates a new query update struct for provided locale.
     *
     * If query is provided, initial data is copied from the query.
     */
    public function newQueryUpdateStruct(string $locale, ?Query $query = null): QueryUpdateStruct;

    /**
     * Creates a new slot create struct.
     */
    public function newSlotCreateStruct(): SlotCreateStruct;

    /**
     * Creates a new slot update struct.
     *
     * If slot is provided, initial data is copied from the slot.
     */
    public function newSlotUpdateStruct(?Slot $slot = null): SlotUpdateStruct;
}
