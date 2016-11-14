<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\QueryUpdateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\Exception\NotFoundException;

class CollectionHandler implements CollectionHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler
     */
    protected $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    protected $positionHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler $queryHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper $collectionMapper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper $positionHelper
     */
    public function __construct(
        CollectionQueryHandler $queryHandler,
        CollectionMapper $collectionMapper,
        PositionHelper $positionHelper
    ) {
        $this->queryHandler = $queryHandler;
        $this->collectionMapper = $collectionMapper;
        $this->positionHelper = $positionHelper;
    }

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
    public function loadCollection($collectionId, $status)
    {
        $data = $this->queryHandler->loadCollectionData($collectionId, $status);

        if (empty($data)) {
            throw new NotFoundException('collection', $collectionId);
        }

        $data = $this->collectionMapper->mapCollections($data);

        return reset($data);
    }

    /**
     * Loads all shared collections.
     *
     * @param int $status
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection[]
     */
    public function loadSharedCollections($status, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadSharedCollectionsData($status, $offset, $limit);

        if (empty($data)) {
            return array();
        }

        $data = $this->collectionMapper->mapCollections($data);

        return $data;
    }

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
    public function loadItem($itemId, $status)
    {
        $data = $this->queryHandler->loadItemData($itemId, $status);

        if (empty($data)) {
            throw new NotFoundException('item', $itemId);
        }

        $data = $this->collectionMapper->mapItems($data);

        return reset($data);
    }

    /**
     * Loads all items that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function loadCollectionItems(Collection $collection)
    {
        return $this->collectionMapper->mapItems(
            $this->queryHandler->loadCollectionItemsData(
                $collection->id,
                $collection->status
            )
        );
    }

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
    public function loadQuery($queryId, $status)
    {
        $data = $this->queryHandler->loadQueryData($queryId, $status);

        if (empty($data)) {
            throw new NotFoundException('query', $queryId);
        }

        $data = $this->collectionMapper->mapQueries($data);

        return reset($data);
    }

    /**
     * Loads all queries that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query[]
     */
    public function loadCollectionQueries(Collection $collection)
    {
        return $this->collectionMapper->mapQueries(
            $this->queryHandler->loadCollectionQueriesData(
                $collection->id,
                $collection->status
            )
        );
    }

    /**
     * Returns if collection with specified ID exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, $status)
    {
        return $this->queryHandler->collectionExists($collectionId, $status);
    }

    /**
     * Returns if collection with specified ID is shared.
     *
     * @param int|string $collectionId
     *
     * @return bool
     */
    public function isSharedCollection($collectionId)
    {
        $data = $this->queryHandler->loadCollectionData($collectionId);

        return isset($data[0]['shared']) && (bool)$data[0]['shared'] === true;
    }

    /**
     * Returns if collection name exists.
     *
     * @param string $name
     * @param int|string $excludedCollectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionNameExists($name, $excludedCollectionId = null, $status = null)
    {
        return $this->queryHandler->collectionNameExists($name, $excludedCollectionId, $status);
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\CollectionCreateStruct $collectionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct)
    {
        $collectionCreateStruct->shared = $collectionCreateStruct->shared ? true : false;

        $collectionCreateStruct->name = $collectionCreateStruct->name !== null ?
            trim($collectionCreateStruct->name) :
            null;

        $createdCollectionId = $this->queryHandler->createCollection($collectionCreateStruct);

        return $this->loadCollection($createdCollectionId, $collectionCreateStruct->status);
    }

    /**
     * Updates a collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $collectionUpdateStruct->type = $collectionUpdateStruct->type !== null ?
            $collectionUpdateStruct->type :
            $collection->type;

        $collectionUpdateStruct->name = $collectionUpdateStruct->name !== null ?
            trim($collectionUpdateStruct->name) :
            $collection->name;

        $this->queryHandler->updateCollection(
            $collection->id,
            $collection->status,
            $collectionUpdateStruct
        );

        return $this->loadCollection($collection->id, $collection->status);
    }

    /**
     * Copies a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $newName
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection, $newName = null)
    {
        $copiedCollectionId = $this->queryHandler->createCollection(
            new CollectionCreateStruct(
                array(
                    'status' => $collection->status,
                    'type' => $collection->type,
                    'shared' => $collection->shared,
                    'name' => $newName !== null ? $newName : null,
                )
            )
        );

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $this->queryHandler->addItem(
                $copiedCollectionId,
                $collectionItem->status,
                new ItemCreateStruct(
                    array(
                        'position' => $collectionItem->position,
                        'valueId' => $collectionItem->valueId,
                        'valueType' => $collectionItem->valueType,
                        'type' => $collectionItem->type,
                    )
                )
            );
        }

        $collectionQueries = $this->loadCollectionQueries($collection);

        foreach ($collectionQueries as $collectionQuery) {
            $this->queryHandler->addQuery(
                $copiedCollectionId,
                $collectionQuery->status,
                new QueryCreateStruct(
                    array(
                        'position' => $collectionQuery->position,
                        'identifier' => $collectionQuery->identifier,
                        'type' => $collectionQuery->type,
                        'parameters' => $collectionQuery->parameters,
                    )
                )
            );
        }

        return $this->loadCollection($copiedCollectionId, $collection->status);
    }

    /**
     * Creates a new collection status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollectionStatus(Collection $collection, $newStatus)
    {
        $this->queryHandler->createCollection(
            new CollectionCreateStruct(
                array(
                    'status' => $newStatus,
                    'type' => $collection->type,
                    'shared' => $collection->shared,
                    'name' => $collection->name,
                )
            ),
            $collection->id
        );

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $this->queryHandler->addItem(
                $collectionItem->collectionId,
                $newStatus,
                new ItemCreateStruct(
                    array(
                        'position' => $collectionItem->position,
                        'valueId' => $collectionItem->valueId,
                        'valueType' => $collectionItem->valueType,
                        'type' => $collectionItem->type,
                    )
                ),
                $collectionItem->id
            );
        }

        $collectionQueries = $this->loadCollectionQueries($collection);

        foreach ($collectionQueries as $collectionQuery) {
            $this->queryHandler->addQuery(
                $collectionQuery->collectionId,
                $newStatus,
                new QueryCreateStruct(
                    array(
                        'position' => $collectionQuery->position,
                        'identifier' => $collectionQuery->identifier,
                        'type' => $collectionQuery->type,
                        'parameters' => $collectionQuery->parameters,
                    )
                ),
                $collectionQuery->id
            );
        }

        return $this->loadCollection($collection->id, $newStatus);
    }

    /**
     * Deletes a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollection($collectionId, $status = null)
    {
        $this->queryHandler->deleteCollectionItems($collectionId, $status);
        $this->queryHandler->deleteCollectionQueries($collectionId, $status);
        $this->queryHandler->deleteCollection($collectionId, $status);
    }

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\ItemCreateStruct $itemCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct)
    {
        $itemCreateStruct->position = $this->positionHelper->createPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $collection->status
            ),
            $itemCreateStruct->position,
            $collection->type !== Collection::TYPE_MANUAL
        );

        $createdItemId = $this->queryHandler->addItem(
            $collection->id,
            $collection->status,
            $itemCreateStruct
        );

        return $this->loadItem($createdItemId, $collection->status);
    }

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
    public function moveItem(Item $item, $position)
    {
        $collection = $this->loadCollection($item->collectionId, $item->status);

        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $item->status
            ),
            $item->position,
            $position,
            $collection->type !== Collection::TYPE_MANUAL
        );

        $this->queryHandler->moveItem($item->id, $item->status, $position);

        return $this->loadItem($item->id, $item->status);
    }

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     */
    public function deleteItem(Item $item)
    {
        $this->queryHandler->deleteItem($item->id, $item->status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperItemConditions(
                $item->collectionId,
                $item->status
            ),
            $item->position
        );
    }

    /**
     * Returns if query with specified identifier exists within the collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $identifier
     *
     * @return bool
     */
    public function queryIdentifierExists(Collection $collection, $identifier)
    {
        return $this->queryHandler->queryIdentifierExists($collection->id, $collection->status, $identifier);
    }

    /**
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function addQuery(Collection $collection, QueryCreateStruct $queryCreateStruct)
    {
        $queryCreateStruct->position = $this->positionHelper->createPosition(
            $this->getPositionHelperQueryConditions(
                $collection->id,
                $collection->status
            ),
            $queryCreateStruct->position
        );

        $createdQueryId = $this->queryHandler->addQuery(
            $collection->id,
            $collection->status,
            $queryCreateStruct
        );

        return $this->loadQuery($createdQueryId, $collection->status);
    }

    /**
     * Updates a query with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Persistence\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct)
    {
        $queryUpdateStruct->identifier = $queryUpdateStruct->identifier ?: $query->identifier;

        $queryUpdateStruct->parameters = is_array($queryUpdateStruct->parameters) ?
            $queryUpdateStruct->parameters :
            $query->parameters;

        $this->queryHandler->updateQuery(
            $query->id,
            $query->status,
            $queryUpdateStruct
        );

        return $this->loadQuery($query->id, $query->status);
    }

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
    public function moveQuery(Query $query, $position)
    {
        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperQueryConditions(
                $query->collectionId,
                $query->status
            ),
            $query->position,
            $position
        );

        $this->queryHandler->moveQuery($query->id, $query->status, $position);

        return $this->loadQuery($query->id, $query->status);
    }

    /**
     * Removes a query.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     */
    public function deleteQuery(Query $query)
    {
        $this->queryHandler->deleteQuery($query->id, $query->status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperQueryConditions(
                $query->collectionId,
                $query->status
            ),
            $query->position
        );
    }

    /**
     * Builds the condition array that will be used with position helper and items in collections.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    protected function getPositionHelperItemConditions($collectionId, $status)
    {
        return array(
            'table' => 'ngbm_collection_item',
            'column' => 'position',
            'conditions' => array(
                'collection_id' => $collectionId,
                'status' => $status,
            ),
        );
    }

    /**
     * Builds the condition array that will be used with position helper and queries in collections.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    protected function getPositionHelperQueryConditions($collectionId, $status)
    {
        return array(
            'table' => 'ngbm_collection_query',
            'column' => 'position',
            'conditions' => array(
                'collection_id' => $collectionId,
                'status' => $status,
            ),
        );
    }
}
