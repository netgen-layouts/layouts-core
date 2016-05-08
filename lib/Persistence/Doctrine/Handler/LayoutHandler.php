<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler as LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Doctrine\DBAL\Types\Type;

class LayoutHandler implements LayoutHandlerInterface
{
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
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper $layoutMapper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper $queryHelper
     */
    public function __construct(
        LayoutMapper $layoutMapper,
        ConnectionHelper $connectionHelper,
        QueryHelper $queryHelper
    ) {
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
        $data = $this->layoutMapper->mapLayouts(
            $this->loadLayoutData($layoutId, $status)
        );

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
        return $this->layoutMapper->mapZones(
            $this->loadLayoutZonesData($layoutId, $status)
        );
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

        foreach (array_unique($layoutCreateStruct->zoneIdentifiers) as $zoneIdentifier) {
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
        // First copy layout data

        $currentTimeStamp = time();

        $layoutData = $this->loadLayoutData($layoutId);
        $insertedLayoutId = null;

        foreach ($layoutData as $layoutDataRow) {
            $query = $this->queryHelper->getLayoutInsertQuery(
                array(
                    'status' => $layoutDataRow['status'],
                    'parent_id' => $layoutDataRow['parent_id'],
                    'identifier' => $layoutDataRow['identifier'],
                    'name' => $layoutDataRow['name'] . ' (copy) ' . crc32(microtime()),
                    'created' => $currentTimeStamp,
                    'modified' => $currentTimeStamp,
                ),
                $insertedLayoutId
            );

            $query->execute();

            $insertedLayoutId = (int)$this->connectionHelper->lastInsertId('ngbm_layout');
        }

        // Then copy zone data

        $allZoneIdentifiers = array();
        $zoneData = $this->loadLayoutZonesData($layoutId);

        foreach ($zoneData as $zoneDataRow) {
            if (!in_array($zoneDataRow['identifier'], $allZoneIdentifiers)) {
                $allZoneIdentifiers[] = $zoneDataRow['identifier'];
            }

            $query = $this->queryHelper->getZoneInsertQuery(
                array(
                    'identifier' => $zoneDataRow['identifier'],
                    'layout_id' => $insertedLayoutId,
                    'status' => $zoneDataRow['status'],
                )
            );

            $query->execute();
        }

        // Then copy block data

        foreach ($allZoneIdentifiers as $zoneIdentifier) {
            $blockData = $this->loadZoneBlocksData($layoutId, $zoneIdentifier);
            $blockIdMapping = array();

            foreach ($blockData as $blockDataRow) {
                $query = $this->queryHelper->getBlockInsertQuery(
                    array(
                        'status' => $blockDataRow['status'],
                        'layout_id' => $insertedLayoutId,
                        'zone_identifier' => $blockDataRow['zone_identifier'],
                        'position' => $blockDataRow['position'],
                        'definition_identifier' => $blockDataRow['definition_identifier'],
                        'view_type' => $blockDataRow['view_type'],
                        'name' => $blockDataRow['name'],
                        'parameters' => $blockDataRow['parameters'],
                    ),
                    isset($blockIdMapping[$blockDataRow['id']]) ?
                        $blockIdMapping[$blockDataRow['id']] :
                        null
                );

                $query->execute();

                if (!isset($blockIdMapping[$blockDataRow['id']])) {
                    $blockIdMapping[$blockDataRow['id']] = (int)$this->connectionHelper->lastInsertId('ngbm_block');
                }
            }
        }

        return $this->loadLayout($insertedLayoutId, Layout::STATUS_PUBLISHED);
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
        $layoutData = $this->loadLayoutData($layoutId, $status);
        $currentTimeStamp = time();
        $query = $this->queryHelper->getLayoutInsertQuery(
            array(
                'status' => $newStatus,
                'parent_id' => $layoutData[0]['parent_id'],
                'identifier' => $layoutData[0]['identifier'],
                'name' => $layoutData[0]['name'],
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
            ),
            $layoutData[0]['id']
        );

        $query->execute();

        $zoneData = $this->loadLayoutZonesData($layoutData[0]['id'], $status);
        foreach ($zoneData as $zoneDataRow) {
            $zoneQuery = $this->queryHelper->getZoneInsertQuery(
                array(
                    'identifier' => $zoneDataRow['identifier'],
                    'layout_id' => $zoneDataRow['layout_id'],
                    'status' => $newStatus,
                )
            );

            $zoneQuery->execute();

            $blockData = $this->loadZoneBlocksData($layoutData[0]['id'], $zoneDataRow['identifier'], $status);
            foreach ($blockData as $blockDataRow) {
                $blockQuery = $this->queryHelper->getBlockInsertQuery(
                    array(
                        'status' => $newStatus,
                        'layout_id' => $blockDataRow['layout_id'],
                        'zone_identifier' => $blockDataRow['zone_identifier'],
                        'position' => $blockDataRow['position'],
                        'definition_identifier' => $blockDataRow['definition_identifier'],
                        'view_type' => $blockDataRow['view_type'],
                        'name' => $blockDataRow['name'],
                        'parameters' => $blockDataRow['parameters'],
                    ),
                    $blockDataRow['id']
                );

                $blockQuery->execute();
            }
        }

        return $this->loadLayout($layoutData[0]['id'], $newStatus);
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

    /**
     * Loads all data for layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return array
     */
    protected function loadLayoutData($layoutId, $status = null)
    {
        $query = $this->queryHelper->getLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        return $data;
    }

    /**
     * Loads all data for zones that belong to layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    protected function loadLayoutZonesData($layoutId, $status = null)
    {
        $query = $this->queryHelper->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('layout_id', ':layout_id')
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $query->addOrderBy('identifier', 'ASC');

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $data;
    }

    /**
     * Loads all data for blocks from zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return array
     */
    protected function loadZoneBlocksData($layoutId, $zoneIdentifier, $status = null)
    {
        $query = $this->queryHelper->getBlockSelectQuery();
        $query->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING);

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
}
