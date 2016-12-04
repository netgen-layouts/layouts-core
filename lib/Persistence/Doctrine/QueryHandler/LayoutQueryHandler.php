<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Page\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Page\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Page\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Page\ZoneUpdateStruct;
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
    public function loadLayoutData($layoutId, $status = null)
    {
        $query = $this->getLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

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
    public function loadLayoutsData($includeDrafts = false, $shared = null, $offset = 0, $limit = null)
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

        if ($shared !== null) {
            $query->where(
                $query->expr()->eq('ngbm_layout.shared', ':shared')
            );
        }

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

        if ($shared !== null) {
            $query->setParameter('shared', (bool) $shared, Type::BOOLEAN);
        }

        $query->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER);

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
     * Loads all data for zones that belong to layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    public function loadLayoutZonesData($layoutId, $status = null)
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('layout_id', ':layout_id')
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $query->addOrderBy('identifier', 'ASC');

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
     * @param int $status
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null, $status = null)
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

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\LayoutCreateStruct $layoutCreateStruct
     * @param int|string $layoutId
     *
     * @return int
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, $layoutId = null)
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
            ->setValue(
                'id',
                $layoutId !== null ? (int) $layoutId : $this->connectionHelper->getAutoIncrementValue('ngbm_layout')
            )
            ->setParameter('status', $layoutCreateStruct->status, Type::INTEGER)
            ->setParameter('type', $layoutCreateStruct->type, Type::STRING)
            ->setParameter('name', trim($layoutCreateStruct->name), Type::STRING)
            ->setParameter('created', $currentTimeStamp, Type::INTEGER)
            ->setParameter('modified', $currentTimeStamp, Type::INTEGER)
            ->setParameter('shared', $layoutCreateStruct->shared, Type::BOOLEAN);

        $query->execute();

        $createdLayoutId = $layoutId;
        if ($createdLayoutId === null) {
            $createdLayoutId = (int) $this->connectionHelper->lastInsertId('ngbm_layout');
        }

        foreach ($layoutCreateStruct->zoneCreateStructs as $zoneCreateStruct) {
            $this->createZone(
                $createdLayoutId,
                $layoutCreateStruct->status,
                $zoneCreateStruct
            );
        }

        return $createdLayoutId;
    }

    /**
     * Creates a zone in specified layout.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param \Netgen\BlockManager\Persistence\Values\Page\ZoneCreateStruct $zoneCreateStruct
     */
    public function createZone($layoutId, $status, ZoneCreateStruct $zoneCreateStruct)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_zone')
            ->values(
                array(
                    'identifier' => ':identifier',
                    'layout_id' => ':layout_id',
                    'status' => ':status',
                    'linked_layout_id' => ':linked_layout_id',
                    'linked_zone_identifier' => ':linked_zone_identifier',
                )
            )
            ->setParameter('identifier', $zoneCreateStruct->identifier, Type::STRING)
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER)
            ->setParameter('linked_layout_id', $zoneCreateStruct->linkedLayoutId, Type::INTEGER)
            ->setParameter('linked_zone_identifier', $zoneCreateStruct->linkedZoneIdentifier, Type::STRING);

        $query->execute();
    }

    /**
     * Updates a layout.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param \Netgen\BlockManager\Persistence\Values\Page\LayoutUpdateStruct $layoutUpdateStruct
     */
    public function updateLayout($layoutId, $status, LayoutUpdateStruct $layoutUpdateStruct)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_layout')
            ->set('name', ':name')
            ->set('modified', ':modified')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER)
            ->setParameter('name', $layoutUpdateStruct->name, Type::STRING)
            ->setParameter('modified', $layoutUpdateStruct->modified, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Updates a zone.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     * @param \Netgen\BlockManager\Persistence\Values\Page\ZoneUpdateStruct $zoneUpdateStruct
     */
    public function updateZone($layoutId, $status, $identifier, ZoneUpdateStruct $zoneUpdateStruct)
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
                ->setParameter('layout_id', $layoutId, Type::INTEGER)
                ->setParameter('identifier', $identifier, Type::STRING)
                ->setParameter('linked_layout_id', $linkedLayoutId, Type::INTEGER)
                ->setParameter('linked_zone_identifier', $linkedZoneIdentifier, Type::STRING);

            $this->applyStatusCondition($query, $status);

            $query->execute();
        }
    }

    /**
     * Deletes the layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        // Delete all zones

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

        // Then delete the layout itself

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
     * Loads all layout block IDs.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    public function loadLayoutBlockIds($layoutId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->execute()->fetchAll();

        return array_map(
            function (array $row) {
                return $row['id'];
            },
            $result
        );
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
