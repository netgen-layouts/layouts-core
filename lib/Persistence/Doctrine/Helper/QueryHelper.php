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
        $query->select('id', 'status', 'parent_id', 'type', 'name', 'created', 'modified')
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
                    'type' => ':type',
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
            ->setParameter('type', $parameters['type'], Type::STRING)
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
}
