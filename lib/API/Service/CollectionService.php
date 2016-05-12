<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;

interface CollectionService
{
    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollection($collectionId, $status = Collection::STATUS_PUBLISHED);

    /**
     * Loads all named collections.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function loadNamedCollections($status = Collection::STATUS_PUBLISHED);

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItem($itemId, $status = Collection::STATUS_PUBLISHED);

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQuery($queryId, $status = Collection::STATUS_PUBLISHED);

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If named collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct);

    /**
     * Updates a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If named collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct);

    /**
     * Copies a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection);

    /**
     * Creates a new collection status.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection already has the provided status
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollectionStatus(Collection $collection, $status);

    /**
     * Creates a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection is not published
     *                                                              If draft already exists for collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createDraft(Collection $collection);

    /**
     * Publishes a collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function publishCollection(Collection $collection);

    /**
     * Deletes a specified collection.
     *
     * If $deleteAllStatuses is set to true, collection is completely deleted.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param bool $deleteAllStatuses
     */
    public function deleteCollection(Collection $collection, $deleteAllStatuses = false);

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If override item is added to manual collection
     *                                                              If item already exists in provided position (only for non manual collections)
     *                                                              If position is out of range (for manual collections)
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If item already exists in provided position (only for non manual collections)
     *                                                              If position is out of range (for manual collections)
     */
    public function moveItem(Item $item, $position);

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     */
    public function deleteItem(Item $item);

    /**
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If query is added to manual collection
     *                                                              If query with specified identifier already exists within the collection
     *                                                              If position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function addQuery(Collection $collection, QueryCreateStruct $queryCreateStruct, $position = null);

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If query with specified identifier already exists within the collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct);

    /**
     * Moves a query within the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If position is out of range
     */
    public function moveQuery(Query $query, $position);

    /**
     * Removes a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function deleteQuery(Query $query);

    /**
     * Creates a new collection create struct.
     *
     * @param int $type
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
