<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class CollectionHandler implements CollectionHandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Helper\PositionHelper
     */
    protected $positionHelper;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Helper\PositionHelper $positionHelper
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Mapper\CollectionMapper $collectionMapper
     */
    public function __construct(
        Connection $connection,
        ConnectionHelper $connectionHelper,
        PositionHelper $positionHelper,
        CollectionMapper $collectionMapper
    ) {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
        $this->positionHelper = $positionHelper;
        $this->collectionMapper = $collectionMapper;
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
        $query = $this->createCollectionSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $collectionId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('collection', $collectionId);
        }

        $data = $this->collectionMapper->mapCollections($data);

        return reset($data);
    }

    /**
     * Loads all collections belonging to the provided block.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection[]
     */
    public function loadBlockCollections($blockId, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('collection_id', 'identifier')
            ->from('ngbm_block_collection')
            ->where(
                $query->expr()->eq('block_id', ':block_id')
            )
            ->setParameter('block_id', $blockId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        $collections = array();
        foreach ($data as $dataItem) {
            $collections[$dataItem['identifier']] = $this->loadCollection(
                $dataItem['collection_id'],
                $status
            );
        }

        return $collections;
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
        $query = $this->createItemSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $itemId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

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
        $query = $this->createItemSelectQuery();
        $query->where(
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->orderBy('position', 'ASC')
        ->setParameter('collection_id', $collectionId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->collectionMapper->mapItems($data);
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
        $query = $this->createQuerySelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $queryId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

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
        $query = $this->createQuerySelectQuery();
        $query->where(
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collectionId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->collectionMapper->mapQueries($data);
    }

    /**
     * Returns if collection with specified ID exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_collection')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
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
        $query = $this->connection->createQueryBuilder();
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
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct)
    {
        $query = $this->createCollectionInsertQuery(
            array(
                'status' => $collectionCreateStruct->status,
                'type' => $collectionCreateStruct->type,
                'name' => trim($collectionCreateStruct->name),
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
    public function updateCollection($collectionId, $status, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $collection = $this->loadCollection($collectionId, $status);

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_collection')
            ->set('name', ':name')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER)
            ->setParameter('name', $collectionUpdateStruct->name !== null ? trim($collectionUpdateStruct->name) : $collection->name, Type::STRING);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        return $this->loadCollection($collectionId, $status);
    }

    /**
     * Copies a collection with specified ID.
     *
     * @param int|string $collectionId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection($collectionId)
    {
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
        $collection = $this->loadCollection($collectionId, $status);

        $query = $this->createCollectionInsertQuery(
            array(
                'status' => $newStatus,
                'type' => $collection->type,
                'name' => $collection->name,
            ),
            $collection->id
        );

        $query->execute();

        $collectionItems = $this->loadCollectionItems($collection->id, $status);
        foreach ($collectionItems as $collectionItem) {
            $itemQuery = $this->createItemInsertQuery(
                array(
                    'status' => $newStatus,
                    'collection_id' => $collection->id,
                    'position' => $collectionItem->position,
                    'link_type' => $collectionItem->linkType,
                    'value_id' => $collectionItem->valueId,
                    'value_type' => $collectionItem->valueType,
                )
            );

            $itemQuery->execute();
        }

        $collectionQueries = $this->loadCollectionQueries($collection->id, $status);
        foreach ($collectionQueries as $collectionQuery) {
            $queryQuery = $this->createQueryInsertQuery(
                array(
                    'status' => $newStatus,
                    'collection_id' => $collection->id,
                    'position' => $collectionQuery->position,
                    'identifier' => $collectionQuery->identifier,
                    'type' => $collectionQuery->type,
                    'parameters' => $collectionQuery->parameters,
                )
            );

            $queryQuery->execute();
        }

        return $this->loadCollection($collection->id, $newStatus);
    }

    /**
     * Updates the collection from one status to another.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function updateCollectionStatus($collectionId, $status, $newStatus)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_collection')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_collection_item')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_collection_query')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        return $this->loadCollection($collectionId, $newStatus);
    }

    /**
     * Deletes a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollection($collectionId, $status = null)
    {
        // First delete all items

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_collection_item')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete all queries

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_collection_query')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete the collection itself

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_collection')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Delete all connections between blocks and collections
        // If status === null or if we deleted the last status for collection

        if ($status === null || !$this->collectionExists($collectionId)) {
            $query = $this->connection->createQueryBuilder();
            $query
                ->delete('ngbm_block_collection')
                ->where(
                    $query->expr()->eq('collection_id', ':collection_id')
                )
                ->setParameter('collection_id', $collectionId, Type::INTEGER);
        }
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
    public function itemExists($collectionId, $status, $position)
    {
        $query = $this->connection->createQueryBuilder();
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

        $this->connectionHelper->applyStatusCondition($query, $status);

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
        $collection = $this->loadCollection($collectionId, $status);

        if ($collection->type === Collection::TYPE_MANUAL) {
            $position = $this->positionHelper->createPosition(
                $this->getPositionHelperItemConditions(
                    $collectionId,
                    $status
                ),
                $position
            );
        }

        $query = $this->createItemInsertQuery(
            array(
                'status' => $status,
                'collection_id' => $collectionId,
                'position' => $position,
                'link_type' => $itemCreateStruct->linkType,
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
        $collection = $this->loadCollection($item->collectionId, $status);

        if ($collection->type === Collection::TYPE_MANUAL) {
            $position = $this->positionHelper->moveToPosition(
                $this->getPositionHelperItemConditions(
                    $item->collectionId,
                    $status
                ),
                $item->position,
                $position
            );
        }

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_collection_item')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $itemId, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

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
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_item')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $itemId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();
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
    public function queryExists($collectionId, $status, $identifier)
    {
        $query = $this->connection->createQueryBuilder();
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

        $this->connectionHelper->applyStatusCondition($query, $status);

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

        $query = $this->createQueryInsertQuery(
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

        $query = $this->connection->createQueryBuilder();
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

        $this->connectionHelper->applyStatusCondition($query, $status);

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

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_collection_query')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $queryId, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

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
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_query')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $queryId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Builds and returns a collection database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createCollectionSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'status', 'type', 'name')
            ->from('ngbm_collection');

        return $query;
    }

    /**
     * Builds and returns an item database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createItemSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'status', 'collection_id', 'position', 'link_type', 'value_id', 'value_type')
            ->from('ngbm_collection_item');

        return $query;
    }

    /**
     * Builds and returns a query database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createQuerySelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'status', 'collection_id', 'position', 'identifier', 'type', 'parameters')
            ->from('ngbm_collection_query');

        return $query;
    }

    /**
     * Builds and returns a collection database INSERT query.
     *
     * @param array $parameters
     * @param int $collectionId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createCollectionInsertQuery(array $parameters, $collectionId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_collection')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'type' => ':type',
                    'name' => ':name',
                )
            )
            ->setValue(
                'id',
                $collectionId !== null ? (int)$collectionId : $this->connectionHelper->getAutoIncrementValue('ngbm_collection')
            )
            ->setParameter('status', $parameters['status'], Type::INTEGER)
            ->setParameter('type', $parameters['type'], Type::INTEGER)
            ->setParameter('name', trim($parameters['name']), Type::STRING);
    }

    /**
     * Builds and returns an item database INSERT query.
     *
     * @param array $parameters
     * @param int $itemId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createItemInsertQuery(array $parameters, $itemId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_item')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'position' => ':position',
                    'link_type' => ':link_type',
                    'value_id' => ':value_id',
                    'value_type' => ':value_type',
                )
            )
            ->setValue(
                'id',
                $itemId !== null ? (int)$itemId : $this->connectionHelper->getAutoIncrementValue('ngbm_collection_item')
            )
            ->setParameter('status', $parameters['status'], Type::INTEGER)
            ->setParameter('collection_id', $parameters['collection_id'], Type::INTEGER)
            ->setParameter('position', $parameters['position'], Type::INTEGER)
            ->setParameter('link_type', $parameters['link_type'], Type::INTEGER)
            ->setParameter('value_id', $parameters['value_id'], Type::STRING)
            ->setParameter('value_type', trim($parameters['value_type']), Type::STRING);
    }

    /**
     * Builds and returns an query database INSERT query.
     *
     * @param array $parameters
     * @param int $queryId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createQueryInsertQuery(array $parameters, $queryId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_query')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'position' => ':position',
                    'identifier' => ':identifier',
                    'type' => ':type',
                    'parameters' => ':parameters',
                )
            )
            ->setValue(
                'id',
                $queryId !== null ? (int)$queryId : $this->connectionHelper->getAutoIncrementValue('ngbm_collection_query')
            )
            ->setParameter('status', $parameters['status'], Type::INTEGER)
            ->setParameter('collection_id', $parameters['collection_id'], Type::INTEGER)
            ->setParameter('position', $parameters['position'], Type::INTEGER)
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('type', $parameters['type'], Type::STRING)
            ->setParameter('parameters', $parameters['parameters'], Type::JSON_ARRAY);
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
