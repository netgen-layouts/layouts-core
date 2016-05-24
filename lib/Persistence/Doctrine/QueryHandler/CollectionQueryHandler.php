<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Netgen\BlockManager\Persistence\Values\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\QueryUpdateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Doctrine\DBAL\Types\Type;

class CollectionQueryHandler
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper
     */
    protected $queryHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper $queryHelper
     */
    public function __construct(
        ConnectionHelper $connectionHelper,
        QueryHelper $queryHelper
    ) {
        $this->connectionHelper = $connectionHelper;
        $this->queryHelper = $queryHelper;
    }

    /**
     * Loads all collection data for collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    public function loadCollectionData($collectionId, $status = null)
    {
        $query = $this->getCollectionSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        return $query->execute()->fetchAll();
    }

    public function loadNamedCollectionsData($status = null)
    {
        $query = $this->getCollectionSelectQuery();
        $query->where(
            $query->expr()->eq('type', ':type')
        )
        ->setParameter('type', Collection::TYPE_NAMED, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        return $query->execute()->fetchAll();
    }

    public function loadItemData($itemId, $status = null)
    {
        $query = $this->getCollectionItemSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $itemId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        return $query->execute()->fetchAll();
    }

    public function loadQueryData($queryId, $status = null)
    {
        $query = $this->getCollectionQuerySelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $queryId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for items that belong to collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    public function loadCollectionItemsData($collectionId, $status = null)
    {
        $query = $this->getCollectionItemSelectQuery();
        $query->where(
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $query->addOrderBy('position', 'ASC');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for queries that belong to collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    public function loadCollectionQueriesData($collectionId, $status = null)
    {
        $query = $this->getCollectionQuerySelectQuery();
        $query->where(
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $query->addOrderBy('position', 'ASC');

        return $query->execute()->fetchAll();
    }

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

    public function createCollection(CollectionCreateStruct $collectionCreateStruct, $collectionId = null)
    {
        $query = $this->getCollectionInsertQuery(
            array(
                'status' => $collectionCreateStruct->status,
                'type' => $collectionCreateStruct->type,
                'name' => $collectionCreateStruct->name,
            ),
            $collectionId
        );

        $query->execute();

        return (int)$this->connectionHelper->lastInsertId('ngbm_collection');
    }

    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_collection')
            ->set('name', ':name')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collection->id, Type::INTEGER)
            ->setParameter('name', $collectionUpdateStruct->name, Type::STRING);

        $this->queryHelper->applyStatusCondition($query, $collection->status);

        $query->execute();
    }

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

    public function addItem($collectionId, $status, ItemCreateStruct $itemCreateStruct, $position, $itemId = null)
    {
        $query = $this->getCollectionItemInsertQuery(
            array(
                'status' => $status,
                'collection_id' => $collectionId,
                'position' => $position,
                'type' => $itemCreateStruct->type,
                'value_id' => $itemCreateStruct->valueId,
                'value_type' => $itemCreateStruct->valueType,
            ),
            $itemId
        );

        $query->execute();

        return (int)$this->connectionHelper->lastInsertId('ngbm_collection_item');
    }

    public function moveItem(Item $item, $position)
    {
        $query = $this->queryHelper->getQuery();

        $query
            ->update('ngbm_collection_item')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $item->id, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $item->status);

        $query->execute();
    }

    public function deleteItem(Item $item)
    {
        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_collection_item')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $item->id, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $item->status);

        $query->execute();
    }

    public function deleteCollectionItems($collectionId, $status = null)
    {
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
    }

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

    public function addQuery($collectionId, $status, QueryCreateStruct $queryCreateStruct, $position, $queryId = null)
    {
        $query = $this->getCollectionQueryInsertQuery(
            array(
                'status' => $status,
                'collection_id' => $collectionId,
                'position' => $position,
                'identifier' => $queryCreateStruct->identifier,
                'type' => $queryCreateStruct->type,
                'parameters' => $queryCreateStruct->parameters,
            ),
            $queryId
        );

        $query->execute();

        return (int)$this->connectionHelper->lastInsertId('ngbm_collection_query');
    }

    public function updateQuery($queryId, $status, QueryUpdateStruct $queryUpdateStruct)
    {
        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_collection_query')
            ->set('identifier', ':identifier')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $queryId, Type::INTEGER)
            ->setParameter('identifier', $queryUpdateStruct->identifier, Type::STRING)
            ->setParameter('parameters', $queryUpdateStruct->parameters, Type::JSON_ARRAY);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();
    }

    public function moveQuery(Query $originalQuery, $position)
    {
        $query = $this->queryHelper->getQuery();

        $query
            ->update('ngbm_collection_query')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $originalQuery->id, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $originalQuery->status);

        $query->execute();
    }

    public function deleteQuery(Query $originalQuery)
    {
        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_collection_query')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $originalQuery->id, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $originalQuery->status);

        $query->execute();
    }

    public function deleteCollectionQueries($collectionId, $status = null)
    {
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
    }

    /**
     * Builds and returns a collection database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getCollectionSelectQuery()
    {
        $query = $this->queryHelper->getQuery();
        $query->select('id', 'status', 'type', 'name')
            ->from('ngbm_collection');

        return $query;
    }

    /**
     * Builds and returns an item database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getCollectionItemSelectQuery()
    {
        $query = $this->queryHelper->getQuery();
        $query->select('id', 'status', 'collection_id', 'position', 'type', 'value_id', 'value_type')
            ->from('ngbm_collection_item');

        return $query;
    }

    /**
     * Builds and returns a query database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getCollectionQuerySelectQuery()
    {
        $query = $this->queryHelper->getQuery();
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
    protected function getCollectionInsertQuery(array $parameters, $collectionId = null)
    {
        return $this->queryHelper->getQuery()
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
            ->setParameter('name', $parameters['name'], Type::STRING);
    }

    /**
     * Builds and returns an item database INSERT query.
     *
     * @param array $parameters
     * @param int $itemId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getCollectionItemInsertQuery(array $parameters, $itemId = null)
    {
        return $this->queryHelper->getQuery()
            ->insert('ngbm_collection_item')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'position' => ':position',
                    'type' => ':type',
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
            ->setParameter('type', $parameters['type'], Type::INTEGER)
            ->setParameter('value_id', $parameters['value_id'], Type::STRING)
            ->setParameter('value_type', $parameters['value_type'], Type::STRING);
    }

    /**
     * Builds and returns an query database INSERT query.
     *
     * @param array $parameters
     * @param int $queryId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getCollectionQueryInsertQuery(array $parameters, $queryId = null)
    {
        return $this->queryHelper->getQuery()
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
            ->setParameter('parameters', $parameters['parameters'], is_array($parameters['parameters']) ? Type::JSON_ARRAY : Type::STRING);
    }
}
