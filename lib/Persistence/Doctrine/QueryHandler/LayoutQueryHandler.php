<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Value;
use PDO;

final class LayoutQueryHandler extends QueryHandler
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
            $query->expr()->eq('l.id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'l.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
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
                'l',
                'ngbm_layout',
                'l2',
                $query->expr()->andX(
                    $query->expr()->eq('l.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status')
                )
            );
        }

        $query->where(
            $query->expr()->eq('l.shared', ':shared')
        );

        $statusExpr = $query->expr()->eq('l.status', ':status');
        if ($includeDrafts) {
            $statusExpr = $query->expr()->orX(
                $statusExpr,
                $query->expr()->isNull('l2.id')
            );
        }

        $query->andWhere($statusExpr);

        $query->setParameter('shared', (bool) $shared, Type::BOOLEAN);
        $query->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER);

        $this->applyOffsetAndLimit($query, $offset, $limit);
        $query->orderBy('l.name', 'ASC');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all data for layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     *
     * @return array
     */
    public function loadRelatedLayoutsData(Layout $sharedLayout)
    {
        $query = $this->getLayoutSelectQuery();

        $query->innerJoin(
            'l',
            'ngbm_zone',
            'z',
            $query->expr()->andX(
                $query->expr()->eq('z.layout_id', 'l.id'),
                $query->expr()->eq('z.status', 'l.status'),
                $query->expr()->eq('z.linked_layout_id', ':linked_layout_id')
            )
        )
        ->where(
            $query->expr()->andX(
                $query->expr()->eq('l.shared', ':shared'),
                $query->expr()->eq('l.status', ':status')
            )
        )
        ->setParameter('shared', false, Type::BOOLEAN)
        ->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER)
        ->setParameter('linked_layout_id', $sharedLayout->id, Type::INTEGER);

        $query->orderBy('l.name', 'ASC');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads the count of layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     *
     * @return int
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(DISTINCT ngbm_layout.id) AS count')
            ->from('ngbm_layout')
            ->innerJoin(
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

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return isset($data[0]['count']) ? (int) $data[0]['count'] : 0;
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
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

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

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

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

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

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayout(Layout $layout)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'type' => ':type',
                    'name' => ':name',
                    'description' => ':description',
                    'created' => ':created',
                    'modified' => ':modified',
                    'shared' => ':shared',
                    'main_locale' => ':main_locale',
                ]
            )
            ->setValue(
                'id',
                $layout->id !== null ?
                    (int) $layout->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_layout')
            )
            ->setParameter('status', $layout->status, Type::INTEGER)
            ->setParameter('type', $layout->type, Type::STRING)
            ->setParameter('name', $layout->name, Type::STRING)
            ->setParameter('description', $layout->description, Type::STRING)
            ->setParameter('created', $layout->created, Type::INTEGER)
            ->setParameter('modified', $layout->modified, Type::INTEGER)
            ->setParameter('shared', $layout->shared, Type::BOOLEAN)
            ->setParameter('main_locale', $layout->mainLocale, Type::STRING);

        $query->execute();

        if ($layout->id === null) {
            $layout->id = (int) $this->connectionHelper->lastInsertId('ngbm_layout');
        }

        return $layout;
    }

    /**
     * Creates a layout translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param string $locale
     */
    public function createLayoutTranslation(Layout $layout, $locale)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_layout_translation')
            ->values(
                [
                    'layout_id' => ':layout_id',
                    'status' => ':status',
                    'locale' => ':locale',
                ]
            )
            ->setParameter('layout_id', $layout->id, Type::INTEGER)
            ->setParameter('status', $layout->status, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING);

        $query->execute();
    }

    /**
     * Creates a zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     */
    public function createZone(Zone $zone)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_zone')
            ->values(
                [
                    'identifier' => ':identifier',
                    'layout_id' => ':layout_id',
                    'status' => ':status',
                    'root_block_id' => ':root_block_id',
                    'linked_layout_id' => ':linked_layout_id',
                    'linked_zone_identifier' => ':linked_zone_identifier',
                ]
            )
            ->setParameter('identifier', $zone->identifier, Type::STRING)
            ->setParameter('layout_id', $zone->layoutId, Type::INTEGER)
            ->setParameter('status', $zone->status, Type::INTEGER)
            ->setParameter('root_block_id', $zone->rootBlockId, Type::INTEGER)
            ->setParameter('linked_layout_id', $zone->linkedLayoutId, Type::INTEGER)
            ->setParameter('linked_zone_identifier', $zone->linkedZoneIdentifier, Type::STRING);

        $query->execute();
    }

    /**
     * Updates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     */
    public function updateLayout(Layout $layout)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_layout')
            ->set('type', ':type')
            ->set('name', ':name')
            ->set('description', ':description')
            ->set('created', ':created')
            ->set('modified', ':modified')
            ->set('shared', ':shared')
            ->set('main_locale', ':main_locale')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layout->id, Type::INTEGER)
            ->setParameter('type', $layout->type, Type::STRING)
            ->setParameter('name', $layout->name, Type::STRING)
            ->setParameter('description', $layout->description, Type::STRING)
            ->setParameter('created', $layout->created, Type::INTEGER)
            ->setParameter('modified', $layout->modified, Type::INTEGER)
            ->setParameter('shared', $layout->shared, Type::BOOLEAN)
            ->setParameter('main_locale', $layout->mainLocale, Type::STRING);

        $this->applyStatusCondition($query, $layout->status);

        $query->execute();
    }

    /**
     * Updates a zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     */
    public function updateZone(Zone $zone)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_zone')
            ->set('root_block_id', ':root_block_id')
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
            ->setParameter('root_block_id', $zone->rootBlockId, Type::INTEGER)
            ->setParameter('linked_layout_id', $zone->linkedLayoutId, Type::INTEGER)
            ->setParameter('linked_zone_identifier', $zone->linkedZoneIdentifier, Type::STRING);

        $this->applyStatusCondition($query, $zone->status);

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
     * Deletes the zone.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     */
    public function deleteZone($layoutId, $zoneIdentifier, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('identifier', $zoneIdentifier, Type::STRING);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes layout translations.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $locale
     */
    public function deleteLayoutTranslations($layoutId, $status = null, $locale = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_layout_translation')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        if ($locale !== null) {
            $query
                ->andWhere($query->expr()->eq('locale', ':locale'))
                ->setParameter(':locale', $locale, Type::STRING);
        }

        $query->execute();
    }

    /**
     * Builds and returns a layout database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getLayoutSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT l.*, lt.*')
            ->from('ngbm_layout', 'l')
            ->innerJoin(
                'l',
                'ngbm_layout_translation',
                'lt',
                $query->expr()->andX(
                    $query->expr()->eq('lt.layout_id', 'l.id'),
                    $query->expr()->eq('lt.status', 'l.status')
                )
            );

        return $query;
    }

    /**
     * Builds and returns a zone database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getZoneSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT ngbm_zone.*')
            ->from('ngbm_zone');

        return $query;
    }
}
