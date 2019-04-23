<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Value;
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
    public function loadLayoutData($layoutId, int $status): array
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
     * Loads all layout IDs for provided parameters. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function loadLayoutIds(bool $includeDrafts, ?bool $shared = null, int $offset = 0, ?int $limit = null): array
    {
        $query = $this->connection->createQueryBuilder();

        $query->select('DISTINCT l.id, l.name')
            ->from('nglayouts_layout', 'l');

        if ($includeDrafts) {
            $query->leftJoin(
                'l',
                'nglayouts_layout',
                'l2',
                $query->expr()->andX(
                    $query->expr()->eq('l.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status')
                )
            );
        }

        if ($shared !== null) {
            $query->where(
                $query->expr()->eq('l.shared', ':shared')
            );
        }

        $statusExpr = $query->expr()->eq('l.status', ':status');
        if ($includeDrafts) {
            $statusExpr = $query->expr()->orX(
                $statusExpr,
                $query->expr()->isNull('l2.id')
            );
        }

        $query->andWhere($statusExpr);

        if ($shared !== null) {
            $query->setParameter('shared', $shared, Type::BOOLEAN);
        }

        $query->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER);

        $this->applyOffsetAndLimit($query, $offset, $limit);
        $query->orderBy('l.name', 'ASC');

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_column($result, 'id');
    }

    /**
     * Loads all layout IDs for provided parameters. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function getLayoutsCount(bool $includeDrafts, ?bool $shared = null): int
    {
        $query = $this->connection->createQueryBuilder();

        $query->select('count(DISTINCT l.id) AS count')
            ->from('nglayouts_layout', 'l');

        if ($includeDrafts) {
            $query->leftJoin(
                'l',
                'nglayouts_layout',
                'l2',
                $query->expr()->andX(
                    $query->expr()->eq('l.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status')
                )
            );
        }

        if ($shared !== null) {
            $query->where(
                $query->expr()->eq('l.shared', ':shared')
            );
        }

        $statusExpr = $query->expr()->eq('l.status', ':status');
        if ($includeDrafts) {
            $statusExpr = $query->expr()->orX(
                $statusExpr,
                $query->expr()->isNull('l2.id')
            );
        }

        $query->andWhere($statusExpr);

        if ($shared !== null) {
            $query->setParameter('shared', $shared, Type::BOOLEAN);
        }

        $query->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Loads all data for layouts with provided IDs. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param array<int|string> $layoutIds
     * @param bool $includeDrafts
     *
     * @return array
     */
    public function loadLayoutsData(array $layoutIds, bool $includeDrafts): array
    {
        $query = $this->getLayoutSelectQuery();

        if ($includeDrafts) {
            $query->leftJoin(
                'l',
                'nglayouts_layout',
                'l2',
                $query->expr()->andX(
                    $query->expr()->eq('l.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status')
                )
            );
        }

        $query->where(
            $query->expr()->in('l.id', [':layout_ids'])
        );

        $statusExpr = $query->expr()->eq('l.status', ':status');
        if ($includeDrafts) {
            $statusExpr = $query->expr()->orX(
                $statusExpr,
                $query->expr()->isNull('l2.id')
            );
        }

        $query->andWhere($statusExpr);

        $query->setParameter('layout_ids', $layoutIds, Connection::PARAM_INT_ARRAY);
        $query->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER);

        $query->orderBy('l.name', 'ASC');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all data for layouts related to provided shared layout.
     */
    public function loadRelatedLayoutsData(Layout $sharedLayout): array
    {
        $query = $this->getLayoutSelectQuery();

        $query->innerJoin(
            'l',
            'nglayouts_zone',
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
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(DISTINCT nglayouts_layout.id) AS count')
            ->from('nglayouts_layout')
            ->innerJoin(
                'nglayouts_layout',
                'nglayouts_zone',
                'z',
                $query->expr()->andX(
                    $query->expr()->eq('z.layout_id', 'nglayouts_layout.id'),
                    $query->expr()->eq('z.status', 'nglayouts_layout.status'),
                    $query->expr()->eq('z.linked_layout_id', ':linked_layout_id')
                )
            )
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('nglayouts_layout.shared', ':shared'),
                    $query->expr()->eq('nglayouts_layout.status', ':status')
                )
            )
            ->setParameter('shared', false, Type::BOOLEAN)
            ->setParameter('status', Value::STATUS_PUBLISHED, Type::INTEGER)
            ->setParameter('linked_layout_id', $sharedLayout->id, Type::INTEGER);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0);
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
    public function loadZoneData($layoutId, int $status, string $identifier): array
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq('l.id', ':layout_id'),
                $query->expr()->eq('z.identifier', ':identifier')
            )
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER)
        ->setParameter('identifier', $identifier, Type::STRING);

        $this->applyStatusCondition($query, $status, 'l.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all data for zones that belong to provided layout.
     */
    public function loadLayoutZonesData(Layout $layout): array
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('l.id', ':layout_id')
        )
        ->setParameter('layout_id', $layout->id, Type::INTEGER)
        ->orderBy('z.identifier', 'ASC');

        $this->applyStatusCondition($query, $layout->status, 'l.status');

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
    public function layoutExists($layoutId, int $status): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Returns if the layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     *
     * @return bool
     */
    public function layoutNameExists(string $name, $excludedLayoutId = null): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_layout')
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

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Creates a layout.
     */
    public function createLayout(Layout $layout): Layout
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_layout')
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
                    $this->connectionHelper->getAutoIncrementValue('nglayouts_layout')
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

        $layout->id = $layout->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_layout');

        return $layout;
    }

    /**
     * Creates a layout translation.
     */
    public function createLayoutTranslation(Layout $layout, string $locale): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_layout_translation')
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
     */
    public function createZone(Zone $zone): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_zone')
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
     */
    public function updateLayout(Layout $layout): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_layout')
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
     */
    public function updateZone(Zone $zone): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_zone')
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
    public function deleteLayoutZones($layoutId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_zone')
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
    public function deleteLayout($layoutId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_layout')
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
    public function deleteZone($layoutId, string $zoneIdentifier, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_zone')
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
    public function deleteLayoutTranslations($layoutId, ?int $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_layout_translation')
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
     */
    private function getLayoutSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT l.*, lt.*')
            ->from('nglayouts_layout', 'l')
            ->innerJoin(
                'l',
                'nglayouts_layout_translation',
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
     */
    private function getZoneSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT z.*')
            ->from('nglayouts_zone', 'z')
            ->innerJoin(
                'z',
                'nglayouts_layout',
                'l',
                $query->expr()->andX(
                    $query->expr()->eq('l.id', 'z.layout_id'),
                    $query->expr()->eq('l.status', 'z.status')
                )
            );

        return $query;
    }
}
