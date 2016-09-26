<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\CollectionCreateStruct as APICollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct as APICollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct as APIItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct as APIQueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct as APIQueryUpdateStruct;
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
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(APICollectionCreateStruct $collectionCreateStruct, $status)
    {
        $name = null;
        if ($collectionCreateStruct->name !== null) {
            $name = trim($collectionCreateStruct->name);
        }

        $createdCollectionId = $this->queryHandler->createCollection(
            new CollectionCreateStruct(
                array(
                    'status' => $status,
                    'type' => $collectionCreateStruct->type,
                    'shared' => $collectionCreateStruct->shared !== null ? $collectionCreateStruct->shared : false,
                    'name' => $name,
                )
            )
        );

        foreach ($collectionCreateStruct->itemCreateStructs as $position => $itemCreateStruct) {
            $this->queryHandler->addItem(
                new ItemCreateStruct(
                    array(
                        'collectionId' => $createdCollectionId,
                        'position' => $position,
                        'status' => $status,
                        'valueId' => $itemCreateStruct->valueId,
                        'valueType' => $itemCreateStruct->valueType,
                        'type' => $itemCreateStruct->type,
                    )
                )
            );
        }

        foreach ($collectionCreateStruct->queryCreateStructs as $position => $queryCreateStruct) {
            $this->queryHandler->addQuery(
                new QueryCreateStruct(
                    array(
                        'collectionId' => $createdCollectionId,
                        'position' => $position,
                        'status' => $status,
                        'identifier' => $queryCreateStruct->identifier,
                        'type' => $queryCreateStruct->type,
                        'parameters' => $queryCreateStruct->getParameters(),
                    )
                )
            );
        }

        return $this->loadCollection($createdCollectionId, $status);
    }

    /**
     * Updates a collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, APICollectionUpdateStruct $collectionUpdateStruct)
    {
        $name = $collection->name;
        if ($collectionUpdateStruct->name !== null) {
            $name = trim($collectionUpdateStruct->name);
        }

        $this->queryHandler->updateCollection(
            $collection->id,
            $collection->status,
            new CollectionUpdateStruct(
                array(
                    'type' => $collection->type,
                    'name' => $name,
                )
            )
        );

        return $this->loadCollection($collection->id, $collection->status);
    }

    /**
     * Changes the type of specified collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $newType
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function changeCollectionType(Collection $collection, $newType, APIQueryCreateStruct $queryCreateStruct = null)
    {
        foreach ($this->loadCollectionQueries($collection) as $query) {
            $this->deleteQuery($query);
        }

        if ($newType === Collection::TYPE_MANUAL) {
            foreach ($this->loadCollectionItems($collection) as $index => $item) {
                $this->moveItem($item, $index);
            }
        } elseif ($newType === Collection::TYPE_DYNAMIC) {
            $this->addQuery($collection, $queryCreateStruct);
        }

        $this->queryHandler->updateCollection(
            $collection->id,
            $collection->status,
            new CollectionUpdateStruct(
                array(
                    'type' => $newType,
                    'name' => $collection->name,
                )
            )
        );

        return $this->loadCollection($collection->id, $collection->status);
    }

    /**
     * Copies a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection)
    {
        $copiedCollectionId = $this->queryHandler->createCollection(
            new CollectionCreateStruct(
                array(
                    'status' => $collection->status,
                    'type' => $collection->type,
                    'shared' => $collection->shared,
                    'name' => $collection->name !== null ?
                        $collection->name . ' (copy) ' . crc32(microtime()) :
                        null,
                )
            )
        );

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $this->queryHandler->addItem(
                new ItemCreateStruct(
                    array(
                        'collectionId' => $copiedCollectionId,
                        'position' => $collectionItem->position,
                        'status' => $collectionItem->status,
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
                new QueryCreateStruct(
                    array(
                        'collectionId' => $copiedCollectionId,
                        'position' => $collectionQuery->position,
                        'status' => $collectionQuery->status,
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
                new ItemCreateStruct(
                    array(
                        'collectionId' => $collectionItem->collectionId,
                        'position' => $collectionItem->position,
                        'status' => $newStatus,
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
                new QueryCreateStruct(
                    array(
                        'collectionId' => $collectionQuery->collectionId,
                        'position' => $collectionQuery->position,
                        'status' => $newStatus,
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
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem(Collection $collection, APIItemCreateStruct $itemCreateStruct, $position = null)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $collection->status
            ),
            $position,
            $collection->type !== Collection::TYPE_MANUAL
        );

        $createdItemId = $this->queryHandler->addItem(
            new ItemCreateStruct(
                array(
                    'collectionId' => $collection->id,
                    'position' => $position,
                    'status' => $collection->status,
                    'valueId' => $itemCreateStruct->valueId,
                    'valueType' => $itemCreateStruct->valueType,
                    'type' => $itemCreateStruct->type,
                )
            )
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
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function addQuery(Collection $collection, APIQueryCreateStruct $queryCreateStruct, $position = null)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperQueryConditions(
                $collection->id,
                $collection->status
            ),
            $position
        );

        $createdQueryId = $this->queryHandler->addQuery(
            new QueryCreateStruct(
                array(
                    'collectionId' => $collection->id,
                    'position' => $position,
                    'status' => $collection->status,
                    'identifier' => $queryCreateStruct->identifier,
                    'type' => $queryCreateStruct->type,
                    'parameters' => $queryCreateStruct->getParameters(),
                )
            )
        );

        return $this->loadQuery($createdQueryId, $collection->status);
    }

    /**
     * Updates a query with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        $this->queryHandler->updateQuery(
            $query->id,
            $query->status,
            new QueryUpdateStruct(
                array(
                    'identifier' => $queryUpdateStruct->identifier !== null ?
                        $queryUpdateStruct->identifier :
                        $query->identifier,
                    'parameters' => $queryUpdateStruct->getParameters() + $query->parameters,
                )
            )
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
