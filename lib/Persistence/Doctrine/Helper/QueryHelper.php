<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class QueryHelper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     */
    public function __construct(Connection $connection, ConnectionHelper $connectionHelper)
    {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
    }

    /**
     * Returns the instance of Doctrine query builder.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery()
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Applies status condition to the query.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param int $status
     * @param string $statusColumn
     */
    public function applyStatusCondition(QueryBuilder $query, $status, $statusColumn = 'status')
    {
        $query->andWhere($query->expr()->eq($statusColumn, ':status'))
            ->setParameter('status', $status, Type::INTEGER);
    }

    /**
     * Builds and returns a layout database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getLayoutSelectQuery()
    {
        $query = $this->getQuery();
        $query->select('id', 'status', 'parent_id', 'identifier', 'name', 'created', 'modified')
            ->from('ngbm_layout');

        return $query;
    }

    /**
     * Builds and returns a zone database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getZoneSelectQuery()
    {
        $query = $this->getQuery();
        $query->select('identifier', 'layout_id', 'status')
            ->from('ngbm_zone');

        return $query;
    }

    /**
     * Builds and returns a block database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getBlockSelectQuery()
    {
        $query = $this->getQuery();
        $query->select('id', 'status', 'layout_id', 'zone_identifier', 'position', 'definition_identifier', 'view_type', 'name', 'parameters')
            ->from('ngbm_block');

        return $query;
    }

    /**
     * Builds and returns a collection database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getCollectionSelectQuery()
    {
        $query = $this->getQuery();
        $query->select('id', 'status', 'type', 'name')
            ->from('ngbm_collection');

        return $query;
    }

    /**
     * Builds and returns an item database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getCollectionItemSelectQuery()
    {
        $query = $this->getQuery();
        $query->select('id', 'status', 'collection_id', 'position', 'type', 'value_id', 'value_type')
            ->from('ngbm_collection_item');

        return $query;
    }

    /**
     * Builds and returns a query database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getCollectionQuerySelectQuery()
    {
        $query = $this->getQuery();
        $query->select('id', 'status', 'collection_id', 'position', 'identifier', 'type', 'parameters')
            ->from('ngbm_collection_query');

        return $query;
    }

    /**
     * Builds and returns a layout database INSERT query.
     *
     * @param array $parameters
     * @param int $layoutId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getLayoutInsertQuery(array $parameters, $layoutId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'parent_id' => ':parent_id',
                    'identifier' => ':identifier',
                    'name' => ':name',
                    'created' => ':created',
                    'modified' => ':modified',
                )
            )
            ->setValue(
                'id',
                $layoutId !== null ? (int)$layoutId : $this->connectionHelper->getAutoIncrementValue('ngbm_layout')
            )
            ->setParameter('status', $parameters['status'], Type::INTEGER)
            ->setParameter('parent_id', $parameters['parent_id'], Type::INTEGER)
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('created', $parameters['created'], Type::INTEGER)
            ->setParameter('modified', $parameters['modified'], Type::INTEGER);
    }

    /**
     * Builds and returns a zone database INSERT query.
     *
     * @param array $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getZoneInsertQuery(array $parameters)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_zone')
            ->values(
                array(
                    'identifier' => ':identifier',
                    'layout_id' => ':layout_id',
                    'status' => ':status',
                )
            )
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }

    /**
     * Builds and returns a block database INSERT query.
     *
     * @param array $parameters
     * @param int $blockId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getBlockInsertQuery(array $parameters, $blockId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_block')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'layout_id' => ':layout_id',
                    'zone_identifier' => ':zone_identifier',
                    'position' => ':position',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'name' => ':name',
                    'parameters' => ':parameters',
                )
            )
            ->setValue(
                'id',
                $blockId !== null ? (int)$blockId : $this->connectionHelper->getAutoIncrementValue('ngbm_block')
            )
            ->setParameter('status', $parameters['status'], Type::INTEGER)
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('zone_identifier', $parameters['zone_identifier'], Type::STRING)
            ->setParameter('position', $parameters['position'], Type::INTEGER)
            ->setParameter('definition_identifier', $parameters['definition_identifier'], Type::STRING)
            ->setParameter('view_type', $parameters['view_type'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('parameters', $parameters['parameters'], is_array($parameters['parameters']) ? Type::JSON_ARRAY : Type::STRING);
    }

    /**
     * Builds and returns a collection database INSERT query.
     *
     * @param array $parameters
     * @param int $collectionId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getCollectionInsertQuery(array $parameters, $collectionId = null)
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
    public function getCollectionItemInsertQuery(array $parameters, $itemId = null)
    {
        return $this->connection->createQueryBuilder()
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
    public function getCollectionQueryInsertQuery(array $parameters, $queryId = null)
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
            ->setParameter('parameters', $parameters['parameters'], is_array($parameters['parameters']) ? Type::JSON_ARRAY : Type::STRING);
    }
}
