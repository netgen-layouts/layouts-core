<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryUpdateStruct;

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
     * Loads the query that belongs to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query for specified collection does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function loadCollectionQuery(Collection $collection);

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
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct $collectionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct);

    /**
     * Updates a collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct);

    /**
     * Copies a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection);

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
     * @param \Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct $itemCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct);

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
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is a manual one
     *                                                          If collection already has a query
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function addQuery(Collection $collection, QueryCreateStruct $queryCreateStruct);

    /**
     * Updates a query with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Persistence\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct);

    /**
     * Removes a query.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     */
    public function deleteQuery(Query $query);
}
