<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper;
use Netgen\BlockManager\Persistence\Handler\Layout as LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\Block as BlockHandlerInterface;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class Handler implements LayoutHandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Block
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Handler\Block $blockHandler
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper $mapper
     */
    public function __construct(
        Connection $connection,
        ConnectionHelper $connectionHelper,
        BlockHandlerInterface $blockHandler,
        Mapper $mapper
    ) {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
        $this->blockHandler = $blockHandler;
        $this->mapper = $mapper;
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function loadLayout($layoutId, $status)
    {
        $query = $this->createLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        $data = $this->mapper->mapLayouts($data);

        return reset($data);
    }

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier, $status)
    {
        $query = $this->createZoneSelectQuery();
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq('layout_id', ':layout_id'),
                $query->expr()->eq('identifier', ':identifier')
            )
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER)
        ->setParameter('identifier', $identifier, Type::STRING);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('zone', $identifier);
        }

        $data = $this->mapper->mapZones($data);

        return reset($data);
    }

    /**
     * Returns if layout with specified ID exists.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return bool
     */
    public function layoutExists($layoutId, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if zone with specified identifier exists in the layout.
     *
     * @param int|string $layoutId
     * @param string $identifier
     * @param int $status
     *
     * @return bool
     */
    public function zoneExists($layoutId, $identifier, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_zone')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('identifier', ':identifier'),
                    $query->expr()->eq('layout_id', ':layout_id')
                )
            )
            ->setParameter('identifier', $identifier, Type::STRING)
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Loads all zones that belong to layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone[]
     */
    public function loadLayoutZones($layoutId, $status)
    {
        $query = $this->createZoneSelectQuery();
        $query->where(
            $query->expr()->eq('layout_id', ':layout_id')
        )
        ->orderBy('identifier', 'ASC')
        ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->mapper->mapZones($data);
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param int|string $parentLayoutId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, $parentLayoutId = null)
    {
        $currentTimeStamp = time();

        $query = $this->createLayoutInsertQuery(
            array(
                'status' => $layoutCreateStruct->status,
                'parent_id' => $parentLayoutId,
                'identifier' => $layoutCreateStruct->identifier,
                'name' => trim($layoutCreateStruct->name),
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
            )
        );

        $query->execute();

        $createdLayoutId = (int)$this->connectionHelper->lastInsertId('ngbm_layout');

        foreach ($layoutCreateStruct->zoneIdentifiers as $zoneIdentifier) {
            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'identifier' => $zoneIdentifier,
                    'layout_id' => $createdLayoutId,
                    'status' => $layoutCreateStruct->status,
                )
            );

            $zoneQuery->execute();
        }

        return $this->loadLayout($createdLayoutId, $layoutCreateStruct->status);
    }

    /**
     * Copies a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function copyLayout($layoutId)
    {
    }

    /**
     * Creates a new layout status.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayoutStatus($layoutId, $status, $newStatus)
    {
        $layout = $this->loadLayout($layoutId, $status);

        $currentTimeStamp = time();
        $query = $this->createLayoutInsertQuery(
            array(
                'status' => $newStatus,
                'parent_id' => $layout->parentId,
                'identifier' => $layout->identifier,
                'name' => $layout->name,
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
            ),
            $layout->id
        );

        $query->execute();

        $layoutZones = $this->loadLayoutZones($layout->id, $status);
        foreach ($layoutZones as $zone) {
            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'identifier' => $zone->identifier,
                    'layout_id' => $layout->id,
                    'status' => $newStatus,
                )
            );

            $zoneQuery->execute();

            $zoneBlocks = $this->blockHandler->loadZoneBlocks($layout->id, $zone->identifier, $status);
            foreach ($zoneBlocks as $block) {
                $blockQuery = $this->blockHandler->createBlockInsertQuery(
                    array(
                        'status' => $newStatus,
                        'layout_id' => $layout->id,
                        'zone_identifier' => $zone->identifier,
                        'position' => $block->position,
                        'definition_identifier' => $block->definitionIdentifier,
                        'view_type' => $block->viewType,
                        'name' => $block->name,
                        'parameters' => $block->parameters,
                    ),
                    $block->id
                );

                $blockQuery->execute();
            }
        }

        return $this->loadLayout($layout->id, $newStatus);
    }

    /**
     * Updates the layout from one status to another.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function updateLayoutStatus($layoutId, $status, $newStatus)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_layout')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_zone')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        return $this->loadLayout($layoutId, $newStatus);
    }

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        // First delete all blocks

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete all zones

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete the layout itself

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->connectionHelper->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Builds and returns a layout database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createLayoutSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'status', 'parent_id', 'identifier', 'name', 'created', 'modified')
            ->from('ngbm_layout');

        return $query;
    }

    /**
     * Builds and returns a zone database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createZoneSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('identifier', 'layout_id', 'status')
            ->from('ngbm_zone');

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
    public function createLayoutInsertQuery(array $parameters, $layoutId = null)
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
    public function createZoneInsertQuery(array $parameters)
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
}
