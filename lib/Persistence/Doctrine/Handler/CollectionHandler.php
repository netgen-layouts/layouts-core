<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Doctrine\DBAL\Types\Type;

class CollectionHandler implements CollectionHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    protected $positionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper
     */
    protected $queryHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper $collectionMapper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper $positionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper $queryHelper
     */
    public function __construct(
        CollectionMapper $collectionMapper,
        ConnectionHelper $connectionHelper,
        PositionHelper $positionHelper,
        QueryHelper $queryHelper
    ) {
        $this->collectionMapper = $collectionMapper;
        $this->connectionHelper = $connectionHelper;
        $this->positionHelper = $positionHelper;
        $this->queryHelper = $queryHelper;
    }

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
    public function loadCollection($collectionId, $status)
    {
        $data = $this->collectionMapper->mapCollections(
            $this->loadCollectionData($collectionId, $status)
        );

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
        $query = $this->queryHelper->getCollectionSelectQuery();
        $query->where(
            $query->expr()->eq('type', ':type')
        )
        ->setParameter('type', Collection::TYPE_NAMED, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
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
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function loadItem($itemId, $status)
    {
        $query = $this->queryHelper->getCollectionItemSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $itemId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
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
            $this->loadCollectionItemsData($collectionId, $status)
        );
    }

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
    public function loadQuery($queryId, $status)
    {
        $query = $this->queryHelper->getCollectionQuerySelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $queryId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
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
            $this->loadCollectionQueriesData($collectionId, $status)
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
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_collection')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if collection with specified ID is named.
     *
     * @param int|string $collectionId
     * @param $status
     *
     * @return bool
     */
    public function isNamedCollection($collectionId, $status)
    {
        $data = $this->loadCollectionData($collectionId, $status);

        return (int)$data[0]['type'] === Collection::TYPE_NAMED;
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
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('type', ':type'),
                    $query->expr()->eq('name', ':name')
                )
            )
            ->setParameter('type', Collection::TYPE_NAMED, Type::INTEGER)
            ->setParameter('name', trim($name), Type::STRING);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     * @param int $type
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct, $type = Collection::TYPE_NAMED)
    {
        $name = null;
        if ($type === Collection::TYPE_NAMED) {
            $name = trim($collectionCreateStruct->name);
        }

        $query = $this->queryHelper->getCollectionInsertQuery(
            array(
                'status' => $collectionCreateStruct->status,
                'type' => $type,
                'name' => $name,
            )
        );

        $query->execute();

        $createdCollectionId = (int)$this->connectionHelper->lastInsertId('ngbm_collection');

        return $this->loadCollection($createdCollectionId, $collectionCreateStruct->status);
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
    public function updateNamedCollection($collectionId, $status, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $collectionData = $this->loadCollectionData($collectionId, $status);

        $name = $collectionData[0]['name'];
        if ((int)$collectionData[0]['type'] === Collection::TYPE_NAMED && $collectionUpdateStruct->name !== null) {
            $name = trim($collectionUpdateStruct->name);
        }

        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_collection')
            ->set('name', ':name')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER)
            ->setParameter('name', $name, Type::STRING);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        return $this->loadCollection($collectionId, $status);
    }

    /**
     * Copies a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection($collectionId, $status = null)
    {
        // First copy collection data

        $collectionData = $this->loadCollectionData($collectionId, $status);
        $insertedCollectionId = null;

        foreach ($collectionData as $collectionDataRow) {
            $query = $this->queryHelper->getCollectionInsertQuery(
                array(
                    'status' => $collectionDataRow['status'],
                    'type' => $collectionDataRow['type'],
                    'name' => (int)$collectionDataRow['type'] === Collection::TYPE_NAMED ?
                        $collectionDataRow['name'] . ' (copy) ' . crc32(microtime()) :
                        $collectionDataRow['name'],
                ),
                $insertedCollectionId
            );

            $query->execute();

            $insertedCollectionId = (int)$this->connectionHelper->lastInsertId('ngbm_collection');
        }

        // Then copy item data

        $itemData = $this->loadCollectionItemsData($collectionId, $status);
        $itemIdMapping = array();

        foreach ($itemData as $itemDataRow) {
            $query = $this->queryHelper->getCollectionItemInsertQuery(
                array(
                    'status' => $itemDataRow['status'],
                    'collection_id' => $insertedCollectionId,
                    'position' => $itemDataRow['position'],
                    'type' => $itemDataRow['type'],
                    'value_id' => $itemDataRow['value_id'],
                    'value_type' => $itemDataRow['value_type'],
                ),
                isset($itemIdMapping[$itemDataRow['id']]) ?
                    $itemIdMapping[$itemDataRow['id']] :
                    null
            );

            $query->execute();

            if (!isset($itemIdMapping[$itemDataRow['id']])) {
                $itemIdMapping[$itemDataRow['id']] = (int)$this->connectionHelper->lastInsertId('ngbm_collection_item');
            }
        }

        // Then copy collection query data

        $queryData = $this->loadCollectionQueriesData($collectionId, $status);
        $queryIdMapping = array();

        foreach ($queryData as $queryDataRow) {
            $query = $this->queryHelper->getCollectionQueryInsertQuery(
                array(
                    'status' => $queryDataRow['status'],
                    'collection_id' => $insertedCollectionId,
                    'position' => $queryDataRow['position'],
                    'identifier' => $queryDataRow['identifier'],
                    'type' => $queryDataRow['type'],
                    'parameters' => $queryDataRow['parameters'],
                ),
                isset($queryIdMapping[$queryDataRow['id']]) ?
                    $queryIdMapping[$queryDataRow['id']] :
                    null
            );

            $query->execute();

            if (!isset($queryIdMapping[$queryDataRow['id']])) {
                $queryIdMapping[$queryDataRow['id']] = (int)$this->connectionHelper->lastInsertId('ngbm_collection_query');
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
        $collectionData = $this->loadCollectionData($collectionId, $status);

        $query = $this->queryHelper->getCollectionInsertQuery(
            array(
                'status' => $newStatus,
                'type' => $collectionData[0]['type'],
                'name' => $collectionData[0]['name'],
            ),
            $collectionData[0]['id']
        );

        $query->execute();

        $itemData = $this->loadCollectionItemsData($collectionData[0]['id'], $status);
        foreach ($itemData as $itemDataRow) {
            $itemQuery = $this->queryHelper->getCollectionItemInsertQuery(
                array(
                    'status' => $newStatus,
                    'collection_id' => $itemDataRow['collection_id'],
                    'position' => $itemDataRow['position'],
                    'type' => $itemDataRow['type'],
                    'value_id' => $itemDataRow['value_id'],
                    'value_type' => $itemDataRow['value_type'],
                ),
                $itemDataRow['id']
            );

            $itemQuery->execute();
        }

        $queryData = $this->loadCollectionQueriesData($collectionData[0]['id'], $status);
        foreach ($queryData as $queryDataRow) {
            $queryQuery = $this->queryHelper->getCollectionQueryInsertQuery(
                array(
                    'status' => $newStatus,
                    'collection_id' => $queryDataRow['collection_id'],
                    'position' => $queryDataRow['position'],
                    'identifier' => $queryDataRow['identifier'],
                    'type' => $queryDataRow['type'],
                    'parameters' => $queryDataRow['parameters'],
                ),
                $queryDataRow['id']
            );

            $queryQuery->execute();
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
        // Delete all connections between blocks and collections

        $query = $this->queryHelper->getQuery();
        $query
            ->delete('ngbm_block_collection')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status, 'collection_status');
        }

        $query->execute();

        // First delete all items

        $query = $this->queryHelper->getQuery();
        $query
            ->delete('ngbm_collection_item')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete all queries

        $query = $this->queryHelper->getQuery();
        $query->delete('ngbm_collection_query')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete the collection itself

        $query = $this->queryHelper->getQuery();
        $query->delete('ngbm_collection')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Returns if item exists on specified position.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int $position
     *
     * @return bool
     */
    public function itemPositionExists($collectionId, $status, $position)
    {
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_collection_item')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('collection_id', ':collection_id'),
                    $query->expr()->eq('position', ':position')
                )
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

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
    public function addItem($collectionId, $status, ItemCreateStruct $itemCreateStruct, $position = null)
    {
        $collectionData = $this->loadCollectionData($collectionId, $status);

        if ((int)$collectionData[0]['type'] === Collection::TYPE_MANUAL) {
            $position = $this->positionHelper->createPosition(
                $this->getPositionHelperItemConditions(
                    $collectionId,
                    $status
                ),
                $position
            );
        }

        $query = $this->queryHelper->getCollectionItemInsertQuery(
            array(
                'status' => $status,
                'collection_id' => $collectionId,
                'position' => $position,
                'type' => $itemCreateStruct->type,
                'value_id' => $itemCreateStruct->valueId,
                'value_type' => $itemCreateStruct->valueType,
            )
        );

        $query->execute();

        $createdItem = $this->loadItem(
            (int)$this->connectionHelper->lastInsertId('ngbm_collection_item'),
            $status
        );

        return $createdItem;
    }

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
    public function moveItem($itemId, $status, $position)
    {
        $item = $this->loadItem($itemId, $status);
        $collectionData = $this->loadCollectionData($item->collectionId, $status);

        if ((int)$collectionData[0]['type'] === Collection::TYPE_MANUAL) {
            $position = $this->positionHelper->moveToPosition(
                $this->getPositionHelperItemConditions(
                    $item->collectionId,
                    $status
                ),
                $item->position,
                $position
            );
        }

        $query = $this->queryHelper->getQuery();

        $query
            ->update('ngbm_collection_item')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $itemId, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

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
        $collectionData = $this->loadCollectionData($item->collectionId, $status);

        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_collection_item')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $itemId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        if ((int)$collectionData[0]['type'] === Collection::TYPE_MANUAL) {
            $this->positionHelper->removePosition(
                $this->getPositionHelperItemConditions(
                    $item->collectionId,
                    $status
                ),
                $item->position
            );
        }
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
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_collection_query')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('collection_id', ':collection_id'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

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
    public function addQuery($collectionId, $status, QueryCreateStruct $queryCreateStruct, $position = null)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperQueryConditions(
                $collectionId,
                $status
            ),
            $position
        );

        $query = $this->queryHelper->getCollectionQueryInsertQuery(
            array(
                'status' => $status,
                'collection_id' => $collectionId,
                'position' => $position,
                'identifier' => $queryCreateStruct->identifier,
                'type' => $queryCreateStruct->type,
                'parameters' => $queryCreateStruct->getParameters(),
            )
        );

        $query->execute();

        $createdQuery = $this->loadQuery(
            (int)$this->connectionHelper->lastInsertId('ngbm_collection_query'),
            $status
        );

        return $createdQuery;
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
    public function updateQuery($queryId, $status, QueryUpdateStruct $queryUpdateStruct)
    {
        $originalQuery = $this->loadQuery($queryId, $status);
        $parameters = $queryUpdateStruct->getParameters() + $originalQuery->parameters;

        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_collection_query')
            ->set('identifier', ':identifier')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $queryId, Type::INTEGER)
            ->setParameter('identifier', $queryUpdateStruct->identifier !== null ? $queryUpdateStruct->identifier : $originalQuery->identifier, Type::STRING)
            ->setParameter('parameters', $parameters, Type::JSON_ARRAY);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        return $this->loadQuery($queryId, $status);
    }

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
    public function moveQuery($queryId, $status, $position)
    {
        $originalQuery = $this->loadQuery($queryId, $status);

        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperQueryConditions(
                $originalQuery->collectionId,
                $status
            ),
            $originalQuery->position,
            $position
        );

        $query = $this->queryHelper->getQuery();

        $query
            ->update('ngbm_collection_query')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $queryId, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

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
        $originalQuery = $this->loadQuery($queryId, $status);

        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_collection_query')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $queryId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        $this->positionHelper->removePosition(
            $this->getPositionHelperQueryConditions(
                $originalQuery->collectionId,
                $status
            ),
            $originalQuery->position
        );
    }

    /**
     * Loads all collection data for collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return array
     */
    protected function loadCollectionData($collectionId, $status = null)
    {
        $query = $this->queryHelper->getCollectionSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('collection', $collectionId);
        }

        return $data;
    }

    /**
     * Loads all data for items that belong to collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    protected function loadCollectionItemsData($collectionId, $status = null)
    {
        $query = $this->queryHelper->getCollectionItemSelectQuery();
        $query->where(
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $query->addOrderBy('position', 'ASC');

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $data;
    }

    /**
     * Loads all data for queries that belong to collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    protected function loadCollectionQueriesData($collectionId, $status = null)
    {
        $query = $this->queryHelper->getCollectionQuerySelectQuery();
        $query->where(
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $query->addOrderBy('position', 'ASC');

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $data;
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
