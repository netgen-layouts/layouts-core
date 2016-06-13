<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;

interface CollectionHandler
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
    public function loadCollection($collectionId, $status);

    /**
     * Loads all named collections.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection[]
     */
    public function loadNamedCollections($status);

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
    public function loadItem($itemId, $status);

    /**
     * Loads all items that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function loadCollectionItems(Collection $collection);

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
    public function loadQuery($queryId, $status);

    /**
     * Loads all queries that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query[]
     */
    public function loadCollectionQueries(Collection $collection);

    /**
     * Returns if collection with specified ID exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, $status);

    /**
     * Returns if collection with specified ID is named.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function isNamedCollection($collectionId, $status);

    /**
     * Returns if named collection exists.
     *
     * @param int|string $name
     * @param int $status
     *
     * @return bool
     */
    public function namedCollectionExists($name, $status = null);

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct, $status);

    /**
     * Updates a collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct);

    /**
     * Changes the type of specified collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $newType
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function changeCollectionType(Collection $collection, $newType, QueryCreateStruct $queryCreateStruct = null);

    /**
     * Copies a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return int The ID of copied collection
     */
    public function copyCollection($collectionId, $status = null);

    /**
     * Creates a new collection status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollectionStatus(Collection $collection, $newStatus);

    /**
     * Deletes a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollection($collectionId, $status = null);

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct, $position = null);

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
    public function moveItem(Item $item, $position);

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     */
    public function deleteItem(Item $item);

    /**
     * Returns if query with specified identifier exists within the collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $identifier
     *
     * @return bool
     */
    public function queryIdentifierExists(Collection $collection, $identifier);

    /**
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function addQuery(Collection $collection, QueryCreateStruct $queryCreateStruct, $position = null);

    /**
     * Updates a query with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct);

    /**
     * Moves a query to specified position in the collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function moveQuery(Query $query, $position);

    /**
     * Removes a query.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     */
    public function deleteQuery(Query $query);
}
