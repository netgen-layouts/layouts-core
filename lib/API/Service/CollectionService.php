<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Collection\ItemDraft;
use Netgen\BlockManager\API\Values\Collection\QueryDraft;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionDraft;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;

interface CollectionService
{
    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollection($collectionId);

    /**
     * Loads a collection draft with specified ID.
     *
     * @param int|string $collectionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function loadCollectionDraft($collectionId);

    /**
     * Loads all named collections.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function loadNamedCollections();

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
     * @return \Netgen\BlockManager\API\Values\Collection\ItemDraft
     */
    public function loadItemDraft($itemId);

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQuery($queryId);

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryDraft
     */
    public function loadQueryDraft($queryId);

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection with provided name already exists (If creating a named collection)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct);

    /**
     * Updates a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not named
     *                                                          If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function updateCollection(CollectionDraft $collection, CollectionUpdateStruct $collectionUpdateStruct);

    /**
     * Copies a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection);

    /**
     * Creates a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If draft already exists for collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function createDraft(Collection $collection);

    /**
     * Discards a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     */
    public function discardDraft(CollectionDraft $collection);

    /**
     * Publishes a collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function publishCollection(CollectionDraft $collection);

    /**
     * Deletes a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     */
    public function deleteCollection(Collection $collection);

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If override item is added to manual collection
     *                                                          If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemDraft
     */
    public function addItem(CollectionDraft $collection, ItemCreateStruct $itemCreateStruct, $position = null);

    /**
     * Moves an item within the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range (for manual collections)
     */
    public function moveItem(ItemDraft $item, $position);

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     */
    public function deleteItem(ItemDraft $item);

    /**
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is added to manual collection
     *                                                          If query with specified identifier already exists within the collection
     *                                                          If position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryDraft
     */
    public function addQuery(CollectionDraft $collection, QueryCreateStruct $queryCreateStruct, $position = null);

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query with specified identifier already exists within the collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryDRaft
     */
    public function updateQuery(QueryDraft $query, QueryUpdateStruct $queryUpdateStruct);

    /**
     * Moves a query within the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range
     */
    public function moveQuery(QueryDraft $query, $position);

    /**
     * Removes a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     */
    public function deleteQuery(QueryDraft $query);

    /**
     * Creates a new collection create struct.
     *
     * @param string $type
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\CollectionCreateStruct
     */
    public function newCollectionCreateStruct($type, $name = null);

    /**
     * Creates a new collection update struct.
     *
     * @return \Netgen\BlockManager\API\Values\CollectionUpdateStruct
     */
    public function newCollectionUpdateStruct();

    /**
     * Creates a new item create struct.
     *
     * @param int $type
     * @param int|string $valueId
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\API\Values\ItemCreateStruct
     */
    public function newItemCreateStruct($type, $valueId, $valueType);

    /**
     * Creates a new query create struct.
     *
     * @param string $identifier
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\QueryCreateStruct
     */
    public function newQueryCreateStruct($identifier, $type);

    /**
     * Creates a new query update struct.
     *
     * @return \Netgen\BlockManager\API\Values\QueryUpdateStruct
     */
    public function newQueryUpdateStruct();
}
