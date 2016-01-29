<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper;
use Netgen\BlockManager\Persistence\Handler\Layout as LayoutHandlerInterface;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
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
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper $connectionHelper
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper $mapper
     */
    public function __construct(Connection $connection, Helper $connectionHelper, Mapper $mapper)
    {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
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
    public function loadLayout($layoutId, $status = APILayout::STATUS_PUBLISHED)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'parent_id', 'identifier', 'name', 'created', 'modified', 'status')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':layout_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        $data = $this->mapper->mapLayouts($data);

        return reset($data);
    }

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If zone with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function loadZone($zoneId, $status = APILayout::STATUS_PUBLISHED)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'layout_id', 'identifier', 'status')
            ->from('ngbm_zone')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':zone_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('zone_id', $zoneId, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('zone', $zoneId);
        }

        $data = $this->mapper->mapZones($data);

        return reset($data);
    }

    /**
     * Loads all zones that belong to layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone[]
     */
    public function loadLayoutZones($layoutId, $status = APILayout::STATUS_PUBLISHED)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'layout_id', 'identifier', 'status')
            ->from('ngbm_zone')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER);

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
                'id' => $this->connectionHelper->getAutoIncrementValue('ngbm_layout'),
                'parent_id' => $parentLayoutId,
                'identifier' => $layoutCreateStruct->identifier,
                'name' => $layoutCreateStruct->name,
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
                'status' => $layoutCreateStruct->status,
            )
        );

        $query->execute();

        $createdLayoutId = (int)$this->connectionHelper->lastInsertId('ngbm_layout');

        foreach ($layoutCreateStruct->zoneIdentifiers as $zoneIdentifier) {
            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'id' => $this->connectionHelper->getAutoIncrementValue('ngbm_zone'),
                    'layout_id' => $createdLayoutId,
                    'identifier' => $zoneIdentifier,
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
     * @param bool $createNew
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function copyLayout($layoutId, $createNew = true, $status = APILayout::STATUS_PUBLISHED, $newStatus = APILayout::STATUS_DRAFT)
    {
        $originalLayout = $this->loadLayout($layoutId, $status);
        $originalZones = $this->loadLayoutZones($layoutId, $status);

        $currentTimeStamp = time();
        $newLayoutId = $createNew ?
            $this->connectionHelper->getAutoIncrementValue('ngbm_layout') :
            $layoutId;

        $query = $this->createLayoutInsertQuery(
            array(
                'id' => $newLayoutId,
                'parent_id' => $originalLayout->parentId,
                'identifier' => $originalLayout->identifier,
                'name' => $originalLayout->name,
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
                'status' => $newStatus,
            )
        );

        $query->execute();

        $copiedLayoutId = $createNew ?
            (int)$this->connectionHelper->lastInsertId('ngbm_layout') :
            $layoutId;

        foreach ($originalZones as $originalZone) {
            $newZoneId = $createNew ?
                $this->connectionHelper->getAutoIncrementValue('ngbm_zone') :
                $originalZone->id;

            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'id' => $newZoneId,
                    'layout_id' => $copiedLayoutId,
                    'identifier' => $originalZone->identifier,
                    'status' => $newStatus,
                )
            );

            $zoneQuery->execute();
        }

        return $this->loadLayout($copiedLayoutId, $newStatus);
    }

    /**
     * Publishes a layout draft.
     *
     * @param int|string $layoutId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function publishLayout($layoutId)
    {
        $layout = $this->loadLayout($layoutId, APILayout::STATUS_DRAFT);

        $this->deleteLayout($layout->id, APILayout::STATUS_ARCHIVED);
        $this->updateLayoutStatus($layout, APILayout::STATUS_PUBLISHED, APILayout::STATUS_ARCHIVED);
        $this->updateLayoutStatus($layout, APILayout::STATUS_DRAFT, APILayout::STATUS_PUBLISHED);

        return $this->loadLayout($layout->id);
    }

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        // First delete all zones

        $query = $this->connection->createQueryBuilder();

        if ($status !== null) {
            $query->delete('ngbm_zone')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('layout_id', ':layout_id'),
                        $query->expr()->eq('status', ':status')
                    )
                )
                ->setParameter('layout_id', $layoutId, Type::INTEGER)
                ->setParameter('status', $status, Type::INTEGER);
        } else {
            $query->delete('ngbm_zone')
                ->where(
                    $query->expr()->eq('layout_id', ':layout_id')
                )
                ->setParameter('layout_id', $layoutId, Type::INTEGER);
        }

        $query->execute();

        // Then delete the layout itself

        $query = $this->connection->createQueryBuilder();

        if ($status !== null) {
            $query->delete('ngbm_layout')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('id', ':layout_id'),
                        $query->expr()->eq('status', ':status')
                    )
                )
                ->setParameter('layout_id', $layoutId, Type::INTEGER)
                ->setParameter('status', $status, Type::INTEGER);
        } else {
            $query->delete('ngbm_layout')
                ->where(
                    $query->expr()->eq('id', ':layout_id')
                )
                ->setParameter('layout_id', $layoutId, Type::INTEGER);
        }

        $query->execute();
    }

    /**
     * Builds and returns a layout database INSERT query.
     *
     * @param array $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createLayoutInsertQuery(array $parameters)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                array(
                    'id' => ':id',
                    'parent_id' => ':parent_id',
                    'identifier' => ':identifier',
                    'name' => ':name',
                    'created' => ':created',
                    'modified' => ':modified',
                    'status' => ':status',
                )
            )
            ->setParameter('id', $parameters['id'], Type::INTEGER)
            ->setParameter('parent_id', $parameters['parent_id'], Type::INTEGER)
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('created', $parameters['created'], Type::INTEGER)
            ->setParameter('modified', $parameters['modified'], Type::INTEGER)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }

    /**
     * Builds and returns a zone database INSERT query.
     *
     * @param array $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createZoneInsertQuery(array $parameters)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_zone')
            ->values(
                array(
                    'id' => ':id',
                    'layout_id' => ':layout_id',
                    'identifier' => ':identifier',
                    'status' => ':status',
                )
            )
            ->setParameter('id', $parameters['id'], Type::INTEGER)
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }

    /**
     * Updates the layout from one status to another.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param int $status
     * @param int $newStatus
     */
    protected function updateLayoutStatus(Layout $layout, $status, $newStatus)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_layout')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':layout_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', $layout->id, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_zone')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', $layout->id, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in(
                        'zone_id',
                        $this->connection->createQueryBuilder()
                            ->select('zone_id')
                            ->from('ngbm_zone')
                            ->where(
                                $query->expr()->andX(
                                    $query->expr()->eq('layout_id', ':layout_id'),
                                    $query->expr()->eq('status', ':status')
                                )
                            )
                            ->getSQL()
                    ),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', $layout->id, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $query->execute();

        // @TODO Update status of block items
    }
}
