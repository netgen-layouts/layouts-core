<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;

class LayoutQueryHandler extends QueryHandler
{
    /**
     * Loads all data for layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    public function loadLayoutData($layoutId, $status)
    {
        $query = $this->getLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param bool $shared
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function loadLayoutsData($includeDrafts, $shared, $offset = 0, $limit = null)
    {
        $query = $this->getLayoutSelectQuery();

        if ($includeDrafts) {
            $query->leftJoin(
                'ngbm_layout',
                'ngbm_layout',
                'l2',
                $query->expr()->andX(
                    $query->expr()->eq('ngbm_layout.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status')
                )
            );
        }

        $query->where(
            $query->expr()->eq('ngbm_layout.shared', ':shared')
        );

        if ($includeDrafts) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->eq('ngbm_layout.status', ':status'),
                    $query->expr()->isNull('l2.id')
                )
            );
        } else {
            $query->andWhere(
                $query->expr()->eq('ngbm_layout.status', ':status')
            );
        }

        $query->setParameter('shared', (bool) $shared, Type::BOOLEAN);
        $query->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER);

        $this->applyOffsetAndLimit($query, $offset, $limit);
        $query->orderBy('id', 'ASC');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
     */
    public function loadRelatedLayoutsData(Layout $sharedLayout, $offset = 0, $limit = null)
    {
        $query = $this->getLayoutSelectQuery();

        $query->innerJoin(
            'ngbm_layout',
            'ngbm_zone',
            'z',
            $query->expr()->andX(
                $query->expr()->eq('z.layout_id', 'ngbm_layout.id'),
                $query->expr()->eq('z.status', 'ngbm_layout.status'),
                $query->expr()->eq('z.linked_layout_id', ':linked_layout_id')
            )
        )
        ->where(
            $query->expr()->andX(
                $query->expr()->eq('ngbm_layout.shared', ':shared'),
                $query->expr()->eq('ngbm_layout.status', ':status')
            )
        )
        ->setParameter('shared', false, Type::BOOLEAN)
        ->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER)
        ->setParameter('linked_layout_id', $sharedLayout->id, Type::INTEGER);

        $this->applyOffsetAndLimit($query, $offset, $limit);
        $query->orderBy('id', 'ASC');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all zone data with provided identifier.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     *
     * @return array
     */
    public function loadZoneData($layoutId, $status, $identifier)
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq('layout_id', ':layout_id'),
                $query->expr()->eq('identifier', ':identifier')
            )
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER)
        ->setParameter('identifier', $identifier, Type::STRING);

        $this->applyStatusCondition($query, $status);

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for zones that belong to provided layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return array
     */
    public function loadLayoutZonesData(Layout $layout)
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('layout_id', ':layout_id')
        )
        ->setParameter('layout_id', $layout->id, Type::INTEGER)
        ->orderBy('identifier', 'ASC');

        $this->applyStatusCondition($query, $layout->status);

        return $query->execute()->fetchAll();
    }

    /**
     * Returns if layout exists.
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

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if the zone exists.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function zoneExists($layoutId, $status, $identifier)
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

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if the layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('name', ':name')
                )
            )
            ->setParameter('name', trim($name), Type::STRING);

        if ($excludedLayoutId !== null) {
            $query->andWhere($query->expr()->neq('id', ':layout_id'))
                ->setParameter('layout_id', $excludedLayoutId, Type::INTEGER);
        }

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @return int
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct)
    {
        $currentTimeStamp = time();

        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'type' => ':type',
                    'name' => ':name',
                    'created' => ':created',
                    'modified' => ':modified',
                    'shared' => ':shared',
                )
            )
            ->setValue('id', $this->connectionHelper->getAutoIncrementValue('ngbm_layout'))
            ->setParameter('status', $layoutCreateStruct->status, Type::INTEGER)
            ->setParameter('type', $layoutCreateStruct->type, Type::STRING)
            ->setParameter('name', trim($layoutCreateStruct->name), Type::STRING)
            ->setParameter('created', $currentTimeStamp, Type::INTEGER)
            ->setParameter('modified', $currentTimeStamp, Type::INTEGER)
            ->setParameter('shared', $layoutCreateStruct->shared, Type::BOOLEAN);

        $query->execute();

        return (int) $this->connectionHelper->lastInsertId('ngbm_layout');
    }

    /**
     * Creates a zone in specified layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct $zoneCreateStruct
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $rootBlock
     */
    public function createZone(ZoneCreateStruct $zoneCreateStruct, Layout $layout, Block $rootBlock)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_zone')
            ->values(
                array(
                    'identifier' => ':identifier',
                    'layout_id' => ':layout_id',
                    'status' => ':status',
                    'root_block_id' => ':root_block_id',
                    'linked_layout_id' => ':linked_layout_id',
                    'linked_zone_identifier' => ':linked_zone_identifier',
                )
            )
            ->setParameter('identifier', $zoneCreateStruct->identifier, Type::STRING)
            ->setParameter('layout_id', $layout->id, Type::INTEGER)
            ->setParameter('status', $layout->status, Type::INTEGER)
            ->setParameter('root_block_id', $rootBlock->id, Type::INTEGER)
            ->setParameter('linked_layout_id', $zoneCreateStruct->linkedLayoutId, Type::INTEGER)
            ->setParameter('linked_zone_identifier', $zoneCreateStruct->linkedZoneIdentifier, Type::STRING);

        $query->execute();
    }

    /**
     * Updates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_layout')
            ->set('name', ':name')
            ->set('modified', ':modified')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layout->id, Type::INTEGER)
            ->setParameter('name', $layoutUpdateStruct->name, Type::STRING)
            ->setParameter('modified', $layoutUpdateStruct->modified, Type::INTEGER);

        $this->applyStatusCondition($query, $layout->status);

        $query->execute();
    }

    /**
     * Updates a zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct $zoneUpdateStruct
     */
    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct)
    {
        if ($zoneUpdateStruct->linkedZone !== null) {
            $linkedLayoutId = null;
            $linkedZoneIdentifier = null;

            if ($zoneUpdateStruct->linkedZone) {
                $linkedLayoutId = $zoneUpdateStruct->linkedZone->layoutId;
                $linkedZoneIdentifier = $zoneUpdateStruct->linkedZone->identifier;
            }

            $query = $this->connection->createQueryBuilder();
            $query
                ->update('ngbm_zone')
                ->set('linked_layout_id', ':linked_layout_id')
                ->set('linked_zone_identifier', ':linked_zone_identifier')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('layout_id', ':layout_id'),
                        $query->expr()->eq('identifier', ':identifier')
                    )
                )
                ->setParameter('layout_id', $zone->layoutId, Type::INTEGER)
                ->setParameter('identifier', $zone->identifier, Type::STRING)
                ->setParameter('linked_layout_id', $linkedLayoutId, Type::INTEGER)
                ->setParameter('linked_zone_identifier', $linkedZoneIdentifier, Type::STRING);

            $this->applyStatusCondition($query, $zone->status);

            $query->execute();
        }
    }

    /**
     * Creates a layout status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param int $newStatus
     */
    public function createLayoutStatus(Layout $layout, $newStatus)
    {
        $currentTimeStamp = time();

        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'type' => ':type',
                    'name' => ':name',
                    'created' => ':created',
                    'modified' => ':modified',
                    'shared' => ':shared',
                )
            )
            ->setValue('id', (int) $layout->id)
            ->setParameter('status', $newStatus, Type::INTEGER)
            ->setParameter('type', $layout->type, Type::STRING)
            ->setParameter('name', $layout->name, Type::STRING)
            ->setParameter('created', $currentTimeStamp, Type::INTEGER)
            ->setParameter('modified', $currentTimeStamp, Type::INTEGER)
            ->setParameter('shared', $layout->shared, Type::BOOLEAN);

        $query->execute();
    }

    /**
     * Creates a zone status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     * @param int $newStatus
     */
    public function createZoneStatus(Zone $zone, $newStatus)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_zone')
            ->values(
                array(
                    'identifier' => ':identifier',
                    'layout_id' => ':layout_id',
                    'status' => ':status',
                    'root_block_id' => ':root_block_id',
                    'linked_layout_id' => ':linked_layout_id',
                    'linked_zone_identifier' => ':linked_zone_identifier',
                )
            )
            ->setParameter('identifier', $zone->identifier, Type::STRING)
            ->setParameter('layout_id', $zone->layoutId, Type::INTEGER)
            ->setParameter('status', $newStatus, Type::INTEGER)
            ->setParameter('root_block_id', $zone->rootBlockId, Type::INTEGER)
            ->setParameter('linked_layout_id', $zone->linkedLayoutId, Type::INTEGER)
            ->setParameter('linked_zone_identifier', $zone->linkedZoneIdentifier, Type::STRING);

        $query->execute();
    }

    /**
     * Deletes all layout zones.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayoutZones($layoutId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes the layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Builds and returns a layout database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getLayoutSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT ngbm_layout.*')
            ->from('ngbm_layout');

        return $query;
    }

    /**
     * Builds and returns a zone database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getZoneSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT ngbm_zone.*')
            ->from('ngbm_zone');

        return $query;
    }
}
