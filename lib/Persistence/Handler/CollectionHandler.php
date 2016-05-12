<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;

interface CollectionHandler
{
    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If collection with specified ID does not exist
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
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function loadItem($itemId, $status);

    /**
     * Loads all items that belong to collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function loadCollectionItems($collectionId, $status);

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function loadQuery($queryId, $status);

    /**
     * Loads all queries that belong to collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query[]
     */
    public function loadCollectionQueries($collectionId, $status);

    /**
     * Returns if collection with specified ID exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, $status = null);

    /**
     * Returns if collection with specified ID is named.
     *
     * @param int|string $collectionId
     *
     * @return bool
     */
    public function isNamedCollection($collectionId);

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
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct);

    /**
     * Updates a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollection($collectionId, $status, CollectionUpdateStruct $collectionUpdateStruct);

    /**
     * Copies a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection($collectionId, $status = null);

    /**
     * Creates a new collection status.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollectionStatus($collectionId, $status, $newStatus);

    /**
     * Deletes a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollection($collectionId, $status = null);

    /**
     * Returns if item exists in the collection.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int|string $itemId
     *
     * @return bool
     */
    public function itemExists($collectionId, $status, $itemId);

    /**
     * Returns if item exists on specified position.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int $position
     *
     * @return bool
     */
    public function itemPositionExists($collectionId, $status, $position);

    /**
     * Adds an item to collection.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem($collectionId, $status, ItemCreateStruct $itemCreateStruct, $position = null);

    /**
     * Moves an item to specified position in the collection.
     *
     * @param int|string $itemId
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function moveItem($itemId, $status, $position);

    /**
     * Removes an item.
     *
     * @param int|string $itemId
     * @param int $status
     */
    public function deleteItem($itemId, $status);

    /**
     * Returns if query with specified ID exists within the collection.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int|string $queryId
     *
     * @return bool
     */
    public function queryExists($collectionId, $status, $queryId);

    /**
     * Returns if query with specified identifier exists within the collection.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function queryIdentifierExists($collectionId, $status, $identifier);

    /**
     * Adds a query to collection.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function addQuery($collectionId, $status, QueryCreateStruct $queryCreateStruct, $position = null);

    /**
     * Updates a query with specified ID.
     *
     * @param int|string $queryId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQuery($queryId, $status, QueryUpdateStruct $queryUpdateStruct);

    /**
     * Moves a query to specified position in the collection.
     *
     * @param int|string $queryId
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function moveQuery($queryId, $status, $position);

    /**
     * Removes a query.
     *
     * @param int|string $queryId
     * @param int $status
     */
    public function deleteQuery($queryId, $status);
}
