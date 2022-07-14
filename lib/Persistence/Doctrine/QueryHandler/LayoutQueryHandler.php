<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Value;

use function array_column;
use function array_map;
use function is_string;
use function trim;

final class LayoutQueryHandler extends QueryHandler
{
    /**
     * Loads all data for layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @return mixed[]
     */
    public function loadLayoutData($layoutId, int $status): array
    {
        $query = $this->getLayoutSelectQuery();

        $this->applyIdCondition($query, $layoutId, 'l.id', 'l.uuid');
        $this->applyStatusCondition($query, $status, 'l.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Loads all layout IDs for provided parameters. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @return int[]
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
                $query->expr()->and(
                    $query->expr()->eq('l.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status'),
                ),
            );
        }

        if ($shared !== null) {
            $query->where(
                $query->expr()->eq('l.shared', ':shared'),
            );
        }

        $statusExpr = $query->expr()->eq('l.status', ':status');
        if ($includeDrafts) {
            $statusExpr = $query->expr()->or(
                $statusExpr,
                $query->expr()->isNull('l2.id'),
            );
        }

        $query->andWhere($statusExpr);

        if ($shared !== null) {
            $query->setParameter('shared', $shared, Types::BOOLEAN);
        }

        $query->setParameter('status', Value::STATUS_PUBLISHED, Types::INTEGER);

        $this->applyOffsetAndLimit($query, $offset, $limit);
        $query->orderBy('l.name', 'ASC');

        $result = $query->execute()->fetchAllAssociative();

        return array_map('intval', array_column($result, 'id'));
    }

    /**
     * Loads the layout count for provided parameters. If $includeDrafts is set to true, drafts which have no
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
                $query->expr()->and(
                    $query->expr()->eq('l.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status'),
                ),
            );
        }

        if ($shared !== null) {
            $query->where(
                $query->expr()->eq('l.shared', ':shared'),
            );
        }

        $statusExpr = $query->expr()->eq('l.status', ':status');
        if ($includeDrafts) {
            $statusExpr = $query->expr()->or(
                $statusExpr,
                $query->expr()->isNull('l2.id'),
            );
        }

        $query->andWhere($statusExpr);

        if ($shared !== null) {
            $query->setParameter('shared', $shared, Types::BOOLEAN);
        }

        $query->setParameter('status', Value::STATUS_PUBLISHED, Types::INTEGER);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Loads all data for layouts with provided IDs. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param int[] $layoutIds
     *
     * @return mixed[]
     */
    public function loadLayoutsData(array $layoutIds, bool $includeDrafts): array
    {
        $query = $this->getLayoutSelectQuery();

        if ($includeDrafts) {
            $query->leftJoin(
                'l',
                'nglayouts_layout',
                'l2',
                $query->expr()->and(
                    $query->expr()->eq('l.id', 'l2.id'),
                    $query->expr()->eq('l2.status', ':status'),
                ),
            );
        }

        $query->where(
            $query->expr()->in('l.id', [':layout_ids']),
        );

        $statusExpr = $query->expr()->eq('l.status', ':status');
        if ($includeDrafts) {
            $statusExpr = $query->expr()->or(
                $statusExpr,
                $query->expr()->isNull('l2.id'),
            );
        }

        $query->andWhere($statusExpr);

        $query->setParameter('layout_ids', $layoutIds, Connection::PARAM_INT_ARRAY);
        $query->setParameter('status', Value::STATUS_PUBLISHED, Types::INTEGER);

        $query->orderBy('l.name', 'ASC');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Loads all data for layouts related to provided shared layout.
     *
     * @return mixed[]
     */
    public function loadRelatedLayoutsData(Layout $sharedLayout): array
    {
        $query = $this->getLayoutSelectQuery();

        $query->innerJoin(
            'l',
            'nglayouts_zone',
            'z',
            $query->expr()->and(
                $query->expr()->eq('z.layout_id', 'l.id'),
                $query->expr()->eq('z.status', 'l.status'),
                $query->expr()->eq('z.linked_layout_uuid', ':linked_layout_uuid'),
            ),
        )
        ->where(
            $query->expr()->and(
                $query->expr()->eq('l.shared', ':shared'),
                $query->expr()->eq('l.status', ':status'),
            ),
        )
        ->setParameter('shared', false, Types::BOOLEAN)
        ->setParameter('status', Value::STATUS_PUBLISHED, Types::INTEGER)
        ->setParameter('linked_layout_uuid', $sharedLayout->uuid, Types::STRING);

        $query->orderBy('l.name', 'ASC');

        return $query->execute()->fetchAllAssociative();
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
                $query->expr()->and(
                    $query->expr()->eq('z.layout_id', 'nglayouts_layout.id'),
                    $query->expr()->eq('z.status', 'nglayouts_layout.status'),
                    $query->expr()->eq('z.linked_layout_uuid', ':linked_layout_uuid'),
                ),
            )
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('nglayouts_layout.shared', ':shared'),
                    $query->expr()->eq('nglayouts_layout.status', ':status'),
                ),
            )
            ->setParameter('shared', false, Types::BOOLEAN)
            ->setParameter('status', Value::STATUS_PUBLISHED, Types::INTEGER)
            ->setParameter('linked_layout_uuid', $sharedLayout->uuid, Types::STRING);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Loads all zone data with provided identifier.
     *
     * @param int|string $layoutId
     *
     * @return mixed[]
     */
    public function loadZoneData($layoutId, int $status, string $identifier): array
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('z.identifier', ':identifier'),
        )
        ->setParameter('identifier', $identifier, Types::STRING);

        $this->applyIdCondition($query, $layoutId, 'l.id', 'l.uuid');
        $this->applyStatusCondition($query, $status, 'l.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Loads all data for zones that belong to provided layout.
     *
     * @return mixed[]
     */
    public function loadLayoutZonesData(Layout $layout): array
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('z.layout_id', ':layout_id'),
        )
        ->setParameter('layout_id', $layout->id, Types::INTEGER)
        ->orderBy('z.identifier', 'ASC');

        $this->applyStatusCondition($query, $layout->status, 'z.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns if the layout exists.
     *
     * @param int|string $layoutId
     */
    public function layoutExists($layoutId, ?int $status = null): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_layout');

        $this->applyIdCondition($query, $layoutId);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Returns if the layout with provided name exists.
     *
     * @param int|string|null $excludedLayoutId
     */
    public function layoutNameExists(string $name, $excludedLayoutId = null): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_layout')
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('name', ':name'),
                ),
            )
            ->setParameter('name', trim($name), Types::STRING);

        if ($excludedLayoutId !== null) {
            $isUuid = is_string($excludedLayoutId);

            $query->andWhere(
                $isUuid ?
                    $query->expr()->neq('uuid', ':uuid') :
                    $query->expr()->neq('id', ':id'),
            )->setParameter(
                $isUuid ? 'uuid' : 'id',
                $excludedLayoutId,
                $isUuid ? Types::STRING : Types::INTEGER,
            );
        }

        $data = $query->execute()->fetchAllAssociative();

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
                    'uuid' => ':uuid',
                    'type' => ':type',
                    'name' => ':name',
                    'description' => ':description',
                    'created' => ':created',
                    'modified' => ':modified',
                    'shared' => ':shared',
                    'main_locale' => ':main_locale',
                ],
            )
            ->setValue('id', $layout->id ?? $this->connectionHelper->nextId('nglayouts_layout'))
            ->setParameter('status', $layout->status, Types::INTEGER)
            ->setParameter('uuid', $layout->uuid, Types::STRING)
            ->setParameter('type', $layout->type, Types::STRING)
            ->setParameter('name', $layout->name, Types::STRING)
            ->setParameter('description', $layout->description, Types::STRING)
            ->setParameter('created', $layout->created, Types::INTEGER)
            ->setParameter('modified', $layout->modified, Types::INTEGER)
            ->setParameter('shared', $layout->shared, Types::BOOLEAN)
            ->setParameter('main_locale', $layout->mainLocale, Types::STRING);

        $query->execute();

        $layout->id ??= (int) $this->connectionHelper->lastId('nglayouts_layout');

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
                ],
            )
            ->setParameter('layout_id', $layout->id, Types::INTEGER)
            ->setParameter('status', $layout->status, Types::INTEGER)
            ->setParameter('locale', $locale, Types::STRING);

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
                    'linked_layout_uuid' => ':linked_layout_uuid',
                    'linked_zone_identifier' => ':linked_zone_identifier',
                ],
            )
            ->setParameter('identifier', $zone->identifier, Types::STRING)
            ->setParameter('layout_id', $zone->layoutId, Types::INTEGER)
            ->setParameter('status', $zone->status, Types::INTEGER)
            ->setParameter('root_block_id', $zone->rootBlockId, Types::INTEGER)
            ->setParameter('linked_layout_uuid', $zone->linkedLayoutUuid, Types::STRING)
            ->setParameter('linked_zone_identifier', $zone->linkedZoneIdentifier, Types::STRING);

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
            ->set('uuid', ':uuid')
            ->set('type', ':type')
            ->set('name', ':name')
            ->set('description', ':description')
            ->set('created', ':created')
            ->set('modified', ':modified')
            ->set('shared', ':shared')
            ->set('main_locale', ':main_locale')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $layout->id, Types::INTEGER)
            ->setParameter('uuid', $layout->uuid, Types::STRING)
            ->setParameter('type', $layout->type, Types::STRING)
            ->setParameter('name', $layout->name, Types::STRING)
            ->setParameter('description', $layout->description, Types::STRING)
            ->setParameter('created', $layout->created, Types::INTEGER)
            ->setParameter('modified', $layout->modified, Types::INTEGER)
            ->setParameter('shared', $layout->shared, Types::BOOLEAN)
            ->setParameter('main_locale', $layout->mainLocale, Types::STRING);

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
            ->set('linked_layout_uuid', ':linked_layout_uuid')
            ->set('linked_zone_identifier', ':linked_zone_identifier')
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('identifier', ':identifier'),
                ),
            )
            ->setParameter('layout_id', $zone->layoutId, Types::INTEGER)
            ->setParameter('identifier', $zone->identifier, Types::STRING)
            ->setParameter('root_block_id', $zone->rootBlockId, Types::INTEGER)
            ->setParameter('linked_layout_uuid', $zone->linkedLayoutUuid, Types::STRING)
            ->setParameter('linked_zone_identifier', $zone->linkedZoneIdentifier, Types::STRING);

        $this->applyStatusCondition($query, $zone->status);

        $query->execute();
    }

    /**
     * Deletes all layout zones.
     */
    public function deleteLayoutZones(int $layoutId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id'),
            )
            ->setParameter('layout_id', $layoutId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes the layout.
     */
    public function deleteLayout(int $layoutId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_layout')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $layoutId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes the zone.
     */
    public function deleteZone(int $layoutId, string $zoneIdentifier, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_zone')
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('identifier', ':identifier'),
                ),
            )
            ->setParameter('layout_id', $layoutId, Types::INTEGER)
            ->setParameter('identifier', $zoneIdentifier, Types::STRING);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes layout translations.
     */
    public function deleteLayoutTranslations(int $layoutId, ?int $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_layout_translation')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id'),
            )
            ->setParameter('layout_id', $layoutId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        if ($locale !== null) {
            $query
                ->andWhere($query->expr()->eq('locale', ':locale'))
                ->setParameter('locale', $locale, Types::STRING);
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
                $query->expr()->and(
                    $query->expr()->eq('lt.layout_id', 'l.id'),
                    $query->expr()->eq('lt.status', 'l.status'),
                ),
            );

        return $query;
    }

    /**
     * Builds and returns a zone database SELECT query.
     */
    private function getZoneSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT z.*, l.uuid as layout_uuid')
            ->from('nglayouts_zone', 'z')
            ->innerJoin(
                'z',
                'nglayouts_layout',
                'l',
                $query->expr()->and(
                    $query->expr()->eq('l.id', 'z.layout_id'),
                    $query->expr()->eq('l.status', 'z.status'),
                ),
            );

        return $query;
    }
}
