<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryTypeInterface;

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
    public function loadCollection($collectionId, array $locales = null, $useMainLocale = true);

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
    public function loadCollectionDraft($collectionId, array $locales = null, $useMainLocale = true);

    /**
     * Updates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct);

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItem($itemId);

    /**
     * Loads an item draft with specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItemDraft($itemId);

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
    public function loadQuery($queryId, array $locales = null, $useMainLocale = true);

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
    public function loadQueryDraft($queryId, array $locales = null, $useMainLocale = true);

    /**
     * Changes the type of specified collection.
     *
     * If new type is a dynamic collection, you also need to provide the QueryCreateStruct used to
     * create the query in the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $newType
     * @param \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection type cannot be changed
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function changeCollectionType(Collection $collection, $newType, QueryCreateStruct $queryCreateStruct = null);

    /**
     * Adds an item to collection at specified position.
     *
     * If position is not provided, item is placed at the end of the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct, $position = null);

    /**
     * Moves an item within the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     *                                                          If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function moveItem(Item $item, $position);

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     */
    public function deleteItem(Item $item);

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     *                                                          If query does not have a specified translation
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct);

    /**
     * Creates a new collection update struct.
     *
     * If collection is provided, initial data is copied from the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct
     */
    public function newCollectionUpdateStruct(Collection $collection = null);

    /**
     * Creates a new item create struct from provided values.
     *
     * @param int $type
     * @param int|string $valueId
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct
     */
    public function newItemCreateStruct($type, $valueId, $valueType);

    /**
     * Creates a new query create struct from provided query type.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType);

    /**
     * Creates a new query update struct for provided locale.
     *
     * If query is provided, initial data is copied from the query.
     *
     * @param string $locale
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct
     */
    public function newQueryUpdateStruct($locale, Query $query = null);
}
