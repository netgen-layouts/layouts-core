<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;

interface CollectionService extends Service
{
    /**
     * Loads a collection with specified ID.
     *
     * By default, collection is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the collection is always available and $useMainLocale is
     * set to true, collection in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param int|string $collectionId
     * @param string[] $locales
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollection($collectionId, ?array $locales = null, bool $useMainLocale = true): Collection;

    /**
     * Loads a collection draft with specified ID.
     *
     * By default, collection is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the collection is always available and $useMainLocale is
     * set to true, collection in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param int|string $collectionId
     * @param string[] $locales
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollectionDraft($collectionId, ?array $locales = null, bool $useMainLocale = true): Collection;

    /**
     * Updates a collection.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct): Collection;

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItem($itemId): Item;

    /**
     * Loads an item draft with specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItemDraft($itemId): Item;

    /**
     * Loads a query with specified ID.
     *
     * By default, query is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the query is always available and $useMainLocale is
     * set to true, query in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param int|string $queryId
     * @param string[] $locales
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQuery($queryId, ?array $locales = null, bool $useMainLocale = true): Query;

    /**
     * Loads a query with specified ID.
     *
     * By default, query is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the query is always available and $useMainLocale is
     * set to true, query in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param int|string $queryId
     * @param string[] $locales
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQueryDraft($queryId, ?array $locales = null, bool $useMainLocale = true): Query;

    /**
     * Changes the type of specified collection.
     *
     * If new type is a dynamic collection, you also need to provide the QueryCreateStruct used to
     * create the query in the collection.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection type cannot be changed
     */
    public function changeCollectionType(Collection $collection, int $newType, ?QueryCreateStruct $queryCreateStruct = null): Collection;

    /**
     * Adds an item to collection at specified position.
     *
     * If position is not provided, item is placed at the end of the collection.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If position is out of range (for manual collections)
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct, ?int $position = null): Item;

    /**
     * Updates a specified item.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     */
    public function updateItem(Item $item, ItemUpdateStruct $itemUpdateStruct): Item;

    /**
     * Moves an item within the collection.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     *                                                          If position is out of range (for manual collections)
     */
    public function moveItem(Item $item, int $position): Item;

    /**
     * Removes an item.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     */
    public function deleteItem(Item $item): void;

    /**
     * Removes all manual items from provided collection.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     */
    public function deleteItems(Collection $collection): Collection;

    /**
     * Updates a query.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     *                                                          If query does not have a specified translation
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct): Query;

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
     * @param \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface $itemDefinition
     * @param int|string $value
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct
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
}
