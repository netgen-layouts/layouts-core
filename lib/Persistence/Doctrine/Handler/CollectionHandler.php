<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\CollectionCreateStruct as APICollectionCreateStruct;
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
     * Loads all named collections.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection[]
     */
    public function loadNamedCollections($status)
    {
        $data = $this->queryHandler->loadNamedCollectionsData($status);

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
     * @param int|string $collectionId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function loadCollectionItems($collectionId, $status)
    {
        return $this->collectionMapper->mapItems(
            $this->queryHandler->loadCollectionItemsData($collectionId, $status)
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
     * @param int|string $collectionId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query[]
     */
    public function loadCollectionQueries($collectionId, $status)
    {
        return $this->collectionMapper->mapQueries(
            $this->queryHandler->loadCollectionQueriesData($collectionId, $status)
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
     * Returns if collection with specified ID is named.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function isNamedCollection($collectionId, $status)
    {
        $data = $this->queryHandler->loadCollectionData($collectionId, $status);

        return isset($data[0]['type']) && (int)$data[0]['type'] === Collection::TYPE_NAMED;
    }

    /**
     * Returns if named collection exists.
     *
     * @param int|string $name
     * @param int $status
     *
     * @return bool
     */
    public function namedCollectionExists($name, $status = null)
    {
        return $this->queryHandler->namedCollectionExists($name, $status);
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
        if ($collectionCreateStruct->type === Collection::TYPE_NAMED) {
            $name = trim($collectionCreateStruct->name);
        }

        $createdCollectionId = $this->queryHandler->createCollection(
            new CollectionCreateStruct(
                array(
                    'status' => $status,
                    'type' => $collectionCreateStruct->type,
                    'name' => $name,
                )
            )
        );

        return $this->loadCollection($createdCollectionId, $status);
    }

    /**
     * Updates a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollection($collectionId, $status, APICollectionUpdateStruct $collectionUpdateStruct)
    {
        $collection = $this->loadCollection($collectionId, $status);

        $name = $collection->name;
        if ($collection->type === Collection::TYPE_NAMED && $collectionUpdateStruct->name !== null) {
            $name = trim($collectionUpdateStruct->name);
        }

        $this->queryHandler->updateCollection(
            $collectionId,
            $status,
            new CollectionUpdateStruct(
                array(
                    'name' => $name,
                )
            )
        );

        return $this->loadCollection($collectionId, $status);
    }

    /**
     * Copies a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return int The ID of copied collection
     */
    public function copyCollection($collectionId, $status = null)
    {
        // First copy collection data

        $collectionData = $this->queryHandler->loadCollectionData($collectionId, $status);
        $insertedCollectionId = null;

        foreach ($collectionData as $collectionDataRow) {
            $insertedCollectionId = $this->queryHandler->createCollection(
                new CollectionCreateStruct(
                    array(
                        'status' => $collectionDataRow['status'],
                        'type' => $collectionDataRow['type'],
                        'name' => (int)$collectionDataRow['type'] === Collection::TYPE_NAMED ?
                            $collectionDataRow['name'] . ' (copy) ' . crc32(microtime()) :
                            $collectionDataRow['name'],
                    )
                ),
                $insertedCollectionId
            );
        }

        // Then copy item data

        $itemData = $this->queryHandler->loadCollectionItemsData($collectionId, $status);
        $itemIdMapping = array();

        foreach ($itemData as $itemDataRow) {
            $insertedItemId = $this->queryHandler->addItem(
                new ItemCreateStruct(
                    array(
                        'collectionId' => $insertedCollectionId,
                        'position' => $itemDataRow['position'],
                        'status' => $itemDataRow['status'],
                        'valueId' => $itemDataRow['value_id'],
                        'valueType' => $itemDataRow['value_type'],
                        'type' => $itemDataRow['type'],
                    )
                ),
                isset($itemIdMapping[$itemDataRow['id']]) ?
                    $itemIdMapping[$itemDataRow['id']] :
                    null
            );

            if (!isset($itemIdMapping[$itemDataRow['id']])) {
                $itemIdMapping[$itemDataRow['id']] = $insertedItemId;
            }
        }

        // Then copy collection query data

        $queryData = $this->queryHandler->loadCollectionQueriesData($collectionId, $status);
        $queryIdMapping = array();

        foreach ($queryData as $queryDataRow) {
            $insertedQueryId = $this->queryHandler->addQuery(
                new QueryCreateStruct(
                    array(
                        'collectionId' => $insertedCollectionId,
                        'position' => $queryDataRow['position'],
                        'status' => $queryDataRow['status'],
                        'identifier' => $queryDataRow['identifier'],
                        'type' => $queryDataRow['type'],
                        'parameters' => $queryDataRow['parameters'],
                    )
                ),
                isset($queryIdMapping[$queryDataRow['id']]) ?
                    $queryIdMapping[$queryDataRow['id']] :
                    null
            );

            if (!isset($queryIdMapping[$queryDataRow['id']])) {
                $queryIdMapping[$queryDataRow['id']] = $insertedQueryId;
            }
        }

        return $insertedCollectionId;
    }

    /**
     * Creates a new collection status.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollectionStatus($collectionId, $status, $newStatus)
    {
        $collectionData = $this->queryHandler->loadCollectionData($collectionId, $status);

        $this->queryHandler->createCollection(
            new CollectionCreateStruct(
                array(
                    'status' => $newStatus,
                    'type' => $collectionData[0]['type'],
                    'name' => $collectionData[0]['name'],
                )
            ),
            $collectionData[0]['id']
        );

        $itemData = $this->queryHandler->loadCollectionItemsData($collectionData[0]['id'], $status);
        foreach ($itemData as $itemDataRow) {
            $this->queryHandler->addItem(
                new ItemCreateStruct(
                    array(
                        'collectionId' => $itemDataRow['collection_id'],
                        'position' => $itemDataRow['position'],
                        'status' => $newStatus,
                        'valueId' => $itemDataRow['value_id'],
                        'valueType' => $itemDataRow['value_type'],
                        'type' => $itemDataRow['type'],
                    )
                ),
                $itemDataRow['id']
            );
        }

        $queryData = $this->queryHandler->loadCollectionQueriesData($collectionData[0]['id'], $status);
        foreach ($queryData as $queryDataRow) {
            $this->queryHandler->addQuery(
                new QueryCreateStruct(
                    array(
                        'collectionId' => $queryDataRow['collection_id'],
                        'position' => $queryDataRow['position'],
                        'status' => $newStatus,
                        'identifier' => $queryDataRow['identifier'],
                        'type' => $queryDataRow['type'],
                        'parameters' => $queryDataRow['parameters'],
                    )
                ),
                $queryDataRow['id']
            );
        }

        return $this->loadCollection($collectionData[0]['id'], $newStatus);
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
     * @param int|string $collectionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem($collectionId, $status, APIItemCreateStruct $itemCreateStruct, $position = null)
    {
        $collection = $this->loadCollection($collectionId, $status);

        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperItemConditions(
                $collectionId,
                $status
            ),
            $position,
            $collection->type !== Collection::TYPE_MANUAL
        );

        $createdItemId = $this->queryHandler->addItem(
            new ItemCreateStruct(
                array(
                    'collectionId' => $collectionId,
                    'position' => $position,
                    'status' => $status,
                    'valueId' => $itemCreateStruct->valueId,
                    'valueType' => $itemCreateStruct->valueType,
                    'type' => $itemCreateStruct->type,
                )
            )
        );

        return $this->loadItem($createdItemId, $status);
    }

    /**
     * Moves an item to specified position in the collection.
     *
     * @param int|string $itemId
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function moveItem($itemId, $status, $position)
    {
        $item = $this->loadItem($itemId, $status);
        $collection = $this->loadCollection($item->collectionId, $status);

        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperItemConditions(
                $item->collectionId,
                $status
            ),
            $item->position,
            $position,
            $collection->type !== Collection::TYPE_MANUAL
        );

        $this->queryHandler->moveItem($itemId, $status, $position);

        return $this->loadItem($itemId, $status);
    }

    /**
     * Removes an item.
     *
     * @param int|string $itemId
     * @param int $status
     */
    public function deleteItem($itemId, $status)
    {
        $item = $this->loadItem($itemId, $status);

        $this->queryHandler->deleteItem($itemId, $status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperItemConditions(
                $item->collectionId,
                $status
            ),
            $item->position
        );
    }

    /**
     * Returns if query with specified identifier exists within the collection.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function queryIdentifierExists($collectionId, $status, $identifier)
    {
        return $this->queryHandler->queryIdentifierExists($collectionId, $status, $identifier);
    }

    /**
     * Adds a query to collection.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function addQuery($collectionId, $status, APIQueryCreateStruct $queryCreateStruct, $position = null)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperQueryConditions(
                $collectionId,
                $status
            ),
            $position
        );

        $createdQueryId = $this->queryHandler->addQuery(
            new QueryCreateStruct(
                array(
                    'collectionId' => $collectionId,
                    'position' => $position,
                    'status' => $status,
                    'identifier' => $queryCreateStruct->identifier,
                    'type' => $queryCreateStruct->type,
                    'parameters' => $queryCreateStruct->getParameters(),
                )
            )
        );

        return $this->loadQuery($createdQueryId, $status);
    }

    /**
     * Updates a query with specified ID.
     *
     * @param int|string $queryId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQuery($queryId, $status, APIQueryUpdateStruct $queryUpdateStruct)
    {
        $query = $this->loadQuery($queryId, $status);

        $this->queryHandler->updateQuery(
            $queryId,
            $status,
            new QueryUpdateStruct(
                array(
                    'identifier' => $queryUpdateStruct->identifier !== null ?
                        $queryUpdateStruct->identifier :
                        $query->identifier,
                    'parameters' => $queryUpdateStruct->getParameters() + $query->parameters,
                )
            )
        );

        return $this->loadQuery($queryId, $status);
    }

    /**
     * Moves a query to specified position in the collection.
     *
     * @param int|string $queryId
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function moveQuery($queryId, $status, $position)
    {
        $query = $this->loadQuery($queryId, $status);

        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperQueryConditions(
                $query->collectionId,
                $status
            ),
            $query->position,
            $position
        );

        $this->queryHandler->moveQuery($queryId, $status, $position);

        return $this->loadQuery($queryId, $status);
    }

    /**
     * Removes a query.
     *
     * @param int|string $queryId
     * @param int $status
     */
    public function deleteQuery($queryId, $status)
    {
        $query = $this->loadQuery($queryId, $status);

        $this->queryHandler->deleteQuery($queryId, $status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperQueryConditions(
                $query->collectionId,
                $status
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
