<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler as LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Doctrine\DBAL\Types\Type;

class LayoutHandler implements LayoutHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper
     */
    protected $layoutMapper;

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
     * @param \Netgen\BlockManager\Persistence\Handler\BlockHandler $blockHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper $layoutMapper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper $queryHelper
     */
    public function __construct(
        BlockHandlerInterface $blockHandler,
        LayoutMapper $layoutMapper,
        ConnectionHelper $connectionHelper,
        QueryHelper $queryHelper
    ) {
        $this->blockHandler = $blockHandler;
        $this->layoutMapper = $layoutMapper;
        $this->connectionHelper = $connectionHelper;
        $this->queryHelper = $queryHelper;
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
        $query = $this->queryHelper->getLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        $data = $this->layoutMapper->mapLayouts($data);

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
        $query = $this->queryHelper->getZoneSelectQuery();
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq('layout_id', ':layout_id'),
                $query->expr()->eq('identifier', ':identifier')
            )
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER)
        ->setParameter('identifier', $identifier, Type::STRING);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('zone', $identifier);
        }

        $data = $this->layoutMapper->mapZones($data);

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
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

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
        $query = $this->queryHelper->getQuery();
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

        $this->queryHelper->applyStatusCondition($query, $status);

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
        $query = $this->queryHelper->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('layout_id', ':layout_id')
        )
        ->orderBy('identifier', 'ASC')
        ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->layoutMapper->mapZones($data);
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

        $query = $this->queryHelper->getLayoutInsertQuery(
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
            $zoneQuery = $this->queryHelper->getZoneInsertQuery(
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
        $query = $this->queryHelper->getLayoutInsertQuery(
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
            $zoneQuery = $this->queryHelper->getZoneInsertQuery(
                array(
                    'identifier' => $zone->identifier,
                    'layout_id' => $layout->id,
                    'status' => $newStatus,
                )
            );

            $zoneQuery->execute();

            $zoneBlocks = $this->blockHandler->loadZoneBlocks($layout->id, $zone->identifier, $status);
            foreach ($zoneBlocks as $block) {
                $blockQuery = $this->queryHelper->getBlockInsertQuery(
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
        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_layout')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_zone')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_block')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

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

        $query = $this->queryHelper->getQuery();
        $query
            ->delete('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete all zones

        $query = $this->queryHelper->getQuery();
        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete the layout itself

        $query = $this->queryHelper->getQuery();
        $query->delete('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();
    }
}
