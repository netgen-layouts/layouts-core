<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Collection\Slot;
use Netgen\Layouts\Persistence\Values\Status;

use function array_column;
use function array_map;

final class CollectionQueryHandler extends QueryHandler
{
    /**
     * Loads all collection data for collection with specified ID.
     *
     * @return mixed[]
     */
    public function loadCollectionData(int|string $collectionId, Status $status): array
    {
        $query = $this->getCollectionWithBlockSelectQuery();

        $this->applyIdCondition($query, $collectionId, 'c.id', 'c.uuid');
        $this->applyStatusCondition($query, $status, 'c.status');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads all collection data for block with specified ID.
     *
     * @return mixed[]
     */
    public function loadBlockCollectionsData(Block $block): array
    {
        $query = $this->getCollectionSelectQuery();

        $this->applyIdCondition($query, $block->id, 'bc.block_id');
        $this->applyStatusCondition($query, $block->status, 'bc.block_status');

        $query->orderBy('bc.identifier', 'ASC');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads all collection reference data.
     *
     * @return mixed[]
     */
    public function loadCollectionReferencesData(Block $block, ?string $identifier = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('block_id', 'block_status', 'collection_id', 'collection_status', 'identifier')
            ->from('nglayouts_block_collection')
            ->where(
                $query->expr()->eq('block_id', ':block_id'),
            )
            ->setParameter('block_id', $block->id, Types::INTEGER)
            ->orderBy('identifier', 'ASC');

        $this->applyStatusCondition($query, $block->status, 'block_status');

        if ($identifier !== null) {
            $query->andWhere($query->expr()->eq('identifier', ':identifier'))
                ->setParameter('identifier', $identifier, Types::STRING);
        }

        return $query->fetchAllAssociative();
    }

    /**
     * Loads all data for an item.
     *
     * @return mixed[]
     */
    public function loadItemData(int|string $itemId, Status $status): array
    {
        $query = $this->getItemSelectQuery();

        $this->applyIdCondition($query, $itemId, 'i.id', 'i.uuid');
        $this->applyStatusCondition($query, $status, 'i.status');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads an item with specified position in specified collection.
     *
     * @return mixed[]
     */
    public function loadItemWithPositionData(Collection $collection, int $position): array
    {
        $query = $this->getItemSelectQuery();
        $query->where(
            $query->expr()->and(
                $query->expr()->eq('i.collection_id', ':collection_id'),
                $query->expr()->eq('i.position', ':position'),
            ),
        )
        ->setParameter('collection_id', $collection->id, Types::INTEGER)
        ->setParameter('position', $position, Types::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 'i.status');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads all data for items that belong to collection with specified ID.
     *
     * @return mixed[]
     */
    public function loadCollectionItemsData(Collection $collection): array
    {
        $query = $this->getItemSelectQuery();
        $query->where(
            $query->expr()->eq('i.collection_id', ':collection_id'),
        )
        ->setParameter('collection_id', $collection->id, Types::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 'i.status');

        $query->addOrderBy('i.position', 'ASC');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads all data for a query.
     *
     * @return mixed[]
     */
    public function loadQueryData(int|string $queryId, Status $status): array
    {
        $query = $this->getQuerySelectQuery();

        $this->applyIdCondition($query, $queryId, 'q.id', 'q.uuid');
        $this->applyStatusCondition($query, $status, 'q.status');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads all collection query IDs.
     *
     * @return int[]
     */
    public function loadCollectionQueryIds(int $collectionId, ?Status $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_collection_query')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id'),
            )
            ->setParameter('collection_id', $collectionId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->fetchAllAssociative();

        return array_map('intval', array_column($result, 'id'));
    }

    /**
     * Loads all data for queries that belong to collection with specified ID.
     *
     * @return mixed[]
     */
    public function loadCollectionQueryData(Collection $collection): array
    {
        $query = $this->getQuerySelectQuery();
        $query->where(
            $query->expr()->eq('q.collection_id', ':collection_id'),
        )
        ->setParameter('collection_id', $collection->id, Types::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 'q.status');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads all data for a slot.
     *
     * @return mixed[]
     */
    public function loadSlotData(int|string $slotId, Status $status): array
    {
        $query = $this->getSlotSelectQuery();

        $this->applyIdCondition($query, $slotId, 's.id', 's.uuid');
        $this->applyStatusCondition($query, $status, 's.status');

        return $query->fetchAllAssociative();
    }

    /**
     * Loads data for slots that belong to collection with specified ID.
     *
     * @return mixed[]
     */
    public function loadCollectionSlotsData(Collection $collection): array
    {
        $query = $this->getSlotSelectQuery();
        $query->where(
            $query->expr()->eq('s.collection_id', ':collection_id'),
        )
        ->setParameter('collection_id', $collection->id, Types::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 's.status');

        $query->addOrderBy('s.position', 'ASC');

        return $query->fetchAllAssociative();
    }

    /**
     * Returns if the collection exists.
     */
    public function collectionExists(int|string $collectionId, Status $status): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_collection');

        $this->applyIdCondition($query, $collectionId);
        $this->applyStatusCondition($query, $status);

        $data = $query->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Creates a collection.
     */
    public function createCollection(Collection $collection): Collection
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_collection')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'start' => ':start',
                    'length' => ':length',
                    'translatable' => ':translatable',
                    'main_locale' => ':main_locale',
                    'always_available' => ':always_available',
                ],
            )
            ->setValue('id', (string) ($collection->id ?? $this->connectionHelper->nextId('nglayouts_collection')))
            ->setParameter('uuid', $collection->uuid, Types::STRING)
            ->setParameter('status', $collection->status->value, Types::INTEGER)
            ->setParameter('start', $collection->offset, Types::INTEGER)
            ->setParameter('length', $collection->limit, Types::INTEGER)
            ->setParameter('translatable', $collection->isTranslatable, Types::BOOLEAN)
            ->setParameter('main_locale', $collection->mainLocale, Types::STRING)
            ->setParameter('always_available', $collection->isAlwaysAvailable, Types::BOOLEAN);

        $query->executeStatement();

        $collection->id ??= $this->connectionHelper->lastId('nglayouts_collection');

        return $collection;
    }

    /**
     * Creates a collection translation.
     */
    public function createCollectionTranslation(Collection $collection, string $locale): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_collection_translation')
            ->values(
                [
                    'collection_id' => ':collection_id',
                    'status' => ':status',
                    'locale' => ':locale',
                ],
            )
            ->setParameter('collection_id', $collection->id, Types::INTEGER)
            ->setParameter('status', $collection->status->value, Types::INTEGER)
            ->setParameter('locale', $locale, Types::STRING);

        $query->executeStatement();
    }

    /**
     * Creates the collection reference.
     */
    public function createCollectionReference(CollectionReference $collectionReference): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->insert('nglayouts_block_collection')
            ->values(
                [
                    'block_id' => ':block_id',
                    'block_status' => ':block_status',
                    'collection_id' => ':collection_id',
                    'collection_status' => ':collection_status',
                    'identifier' => ':identifier',
                ],
            )
            ->setParameter('block_id', $collectionReference->blockId, Types::INTEGER)
            ->setParameter('block_status', $collectionReference->blockStatus->value, Types::INTEGER)
            ->setParameter('collection_id', $collectionReference->collectionId, Types::INTEGER)
            ->setParameter('collection_status', $collectionReference->collectionStatus->value, Types::INTEGER)
            ->setParameter('identifier', $collectionReference->identifier, Types::STRING);

        $query->executeStatement();
    }

    /**
     * Updates a collection.
     */
    public function updateCollection(Collection $collection): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_collection')
            ->set('uuid', ':uuid')
            ->set('start', ':start')
            ->set('length', ':length')
            ->set('translatable', ':translatable')
            ->set('main_locale', ':main_locale')
            ->set('always_available', ':always_available')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $collection->id, Types::INTEGER)
            ->setParameter('uuid', $collection->uuid, Types::STRING)
            ->setParameter('start', $collection->offset, Types::INTEGER)
            ->setParameter('length', $collection->limit, Types::INTEGER)
            ->setParameter('translatable', $collection->isTranslatable, Types::BOOLEAN)
            ->setParameter('main_locale', $collection->mainLocale, Types::STRING)
            ->setParameter('always_available', $collection->isAlwaysAvailable, Types::BOOLEAN);

        $this->applyStatusCondition($query, $collection->status);

        $query->executeStatement();
    }

    /**
     * Deletes a collection.
     */
    public function deleteCollection(int $collectionId, ?Status $status = null): void
    {
        // Delete all connections between blocks and collections

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_block_collection')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id'),
            )
            ->setParameter('collection_id', $collectionId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'collection_status');
        }

        $query->executeStatement();

        // Then delete the collection itself

        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_collection')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $collectionId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->executeStatement();
    }

    /**
     * Deletes collection translations.
     */
    public function deleteCollectionTranslations(int $collectionId, ?Status $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_translation')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id'),
            )
            ->setParameter('collection_id', $collectionId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        if ($locale !== null) {
            $query
                ->andWhere($query->expr()->eq('locale', ':locale'))
                ->setParameter('locale', $locale, Types::STRING);
        }

        $query->executeStatement();
    }

    /**
     * Deletes the collection reference.
     *
     * @param int[] $blockIds
     */
    public function deleteCollectionReferences(array $blockIds, ?Status $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_block_collection')
            ->where(
                $query->expr()->in('block_id', [':block_id']),
            )
            ->setParameter('block_id', $blockIds, ArrayParameterType::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'block_status', 'block_status');
        }

        $query->executeStatement();
    }

    /**
     * Adds an item.
     */
    public function addItem(Item $item): Item
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_collection_item')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'position' => ':position',
                    'value' => ':value',
                    'value_type' => ':value_type',
                    'view_type' => ':view_type',
                    'config' => ':config',
                ],
            )
            ->setValue('id', (string) ($item->id ?? $this->connectionHelper->nextId('nglayouts_collection_item')))
            ->setParameter('uuid', $item->uuid, Types::STRING)
            ->setParameter('status', $item->status->value, Types::INTEGER)
            ->setParameter('collection_id', $item->collectionId, Types::INTEGER)
            ->setParameter('position', $item->position, Types::INTEGER)
            ->setParameter('value', $item->value, Types::STRING)
            ->setParameter('value_type', $item->valueType, Types::STRING)
            ->setParameter('view_type', $item->viewType, Types::STRING)
            ->setParameter('config', $item->config, Types::JSON);

        $query->executeStatement();

        $item->id ??= $this->connectionHelper->lastId('nglayouts_collection_item');

        return $item;
    }

    /**
     * Returns if the slot with provided position exists in the collection.
     */
    public function slotWithPositionExists(Collection $collection, int $position): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_collection_slot')
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('collection_id', ':collection_id'),
                    $query->expr()->eq('position', ':position'),
                ),
            )
            ->setParameter('collection_id', $collection->id, Types::INTEGER)
            ->setParameter('position', $position, Types::INTEGER);

        $this->applyStatusCondition($query, $collection->status);

        $data = $query->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Adds a slot.
     */
    public function addSlot(Slot $slot): Slot
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_collection_slot')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'position' => ':position',
                    'view_type' => ':view_type',
                ],
            )
            ->setValue('id', (string) ($slot->id ?? $this->connectionHelper->nextId('nglayouts_collection_slot')))
            ->setParameter('uuid', $slot->uuid, Types::STRING)
            ->setParameter('status', $slot->status->value, Types::INTEGER)
            ->setParameter('collection_id', $slot->collectionId, Types::INTEGER)
            ->setParameter('position', $slot->position, Types::INTEGER)
            ->setParameter('view_type', $slot->viewType, Types::STRING);

        $query->executeStatement();

        $slot->id ??= $this->connectionHelper->lastId('nglayouts_collection_slot');

        return $slot;
    }

    /**
     * Updates an item.
     */
    public function updateItem(Item $item): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('nglayouts_collection_item')
            ->set('uuid', ':uuid')
            ->set('collection_id', ':collection_id')
            ->set('position', ':position')
            ->set('value', ':value')
            ->set('value_type', ':value_type')
            ->set('view_type', ':view_type')
            ->set('config', ':config')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $item->id, Types::INTEGER)
            ->setParameter('uuid', $item->uuid, Types::STRING)
            ->setParameter('collection_id', $item->collectionId, Types::INTEGER)
            ->setParameter('position', $item->position, Types::INTEGER)
            ->setParameter('value', $item->value, Types::STRING)
            ->setParameter('value_type', $item->valueType, Types::STRING)
            ->setParameter('view_type', $item->viewType, Types::STRING)
            ->setParameter('config', $item->config, Types::JSON);

        $this->applyStatusCondition($query, $item->status);

        $query->executeStatement();
    }

    /**
     * Updates a slot.
     */
    public function updateSlot(Slot $slot): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('nglayouts_collection_slot')
            ->set('uuid', ':uuid')
            ->set('collection_id', ':collection_id')
            ->set('position', ':position')
            ->set('view_type', ':view_type')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $slot->id, Types::INTEGER)
            ->setParameter('uuid', $slot->uuid, Types::STRING)
            ->setParameter('collection_id', $slot->collectionId, Types::INTEGER)
            ->setParameter('position', $slot->position, Types::INTEGER)
            ->setParameter('view_type', $slot->viewType, Types::STRING);

        $this->applyStatusCondition($query, $slot->status);

        $query->executeStatement();
    }

    /**
     * Deletes an item.
     */
    public function deleteItem(int $itemId, Status $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_item')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $itemId, Types::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->executeStatement();
    }

    /**
     * Deletes all collection items.
     */
    public function deleteCollectionItems(int $collectionId, ?Status $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_collection_item')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id'),
            )
            ->setParameter('collection_id', $collectionId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->executeStatement();
    }

    /**
     * Deletes a slot.
     */
    public function deleteSlot(int $slotId, Status $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_slot')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $slotId, Types::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->executeStatement();
    }

    /**
     * Deletes all collection slots.
     */
    public function deleteCollectionSlots(int $collectionId, ?Status $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_collection_slot')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id'),
            )
            ->setParameter('collection_id', $collectionId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->executeStatement();
    }

    /**
     * Creates a query.
     */
    public function createQuery(Query $query): Query
    {
        $dbQuery = $this->connection->createQueryBuilder()
            ->insert('nglayouts_collection_query')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'type' => ':type',
                ],
            )
            ->setValue('id', (string) ($query->id ?? $this->connectionHelper->nextId('nglayouts_collection_query')))
            ->setParameter('uuid', $query->uuid, Types::STRING)
            ->setParameter('status', $query->status->value, Types::INTEGER)
            ->setParameter('collection_id', $query->collectionId, Types::INTEGER)
            ->setParameter('type', $query->type, Types::STRING);

        $dbQuery->executeStatement();

        $query->id ??= $this->connectionHelper->lastId('nglayouts_collection_query');

        return $query;
    }

    /**
     * Creates a query translation.
     */
    public function createQueryTranslation(Query $query, string $locale): void
    {
        $dbQuery = $this->connection->createQueryBuilder()
            ->insert('nglayouts_collection_query_translation')
            ->values(
                [
                    'query_id' => ':query_id',
                    'status' => ':status',
                    'locale' => ':locale',
                    'parameters' => ':parameters',
                ],
            )
            ->setParameter('query_id', $query->id, Types::INTEGER)
            ->setParameter('status', $query->status->value, Types::INTEGER)
            ->setParameter('locale', $locale, Types::STRING)
            ->setParameter('parameters', $query->parameters[$locale], Types::JSON);

        $dbQuery->executeStatement();
    }

    /**
     * Updates a query translation.
     */
    public function updateQueryTranslation(Query $query, string $locale): void
    {
        $dbQuery = $this->connection->createQueryBuilder();

        $dbQuery
            ->update('nglayouts_collection_query_translation')
            ->set('parameters', ':parameters')
            ->where(
                $dbQuery->expr()->and(
                    $dbQuery->expr()->eq('query_id', ':query_id'),
                    $dbQuery->expr()->eq('locale', ':locale'),
                ),
            )
            ->setParameter('query_id', $query->id, Types::INTEGER)
            ->setParameter('locale', $locale, Types::STRING)
            ->setParameter('parameters', $query->parameters[$locale], Types::JSON);

        $this->applyStatusCondition($dbQuery, $query->status);

        $dbQuery->executeStatement();
    }

    /**
     * Deletes the queries with provided IDs.
     *
     * @param int[] $queryIds
     */
    public function deleteQuery(array $queryIds, ?Status $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_query')
            ->where(
                $query->expr()->in('id', [':query_id']),
            )
            ->setParameter('query_id', $queryIds, ArrayParameterType::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->executeStatement();
    }

    /**
     * Deletes the query translations with provided query IDs.
     *
     * @param int[] $queryIds
     */
    public function deleteQueryTranslations(array $queryIds, ?Status $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_query_translation')
            ->where(
                $query->expr()->in('query_id', [':query_id']),
            )
            ->setParameter('query_id', $queryIds, ArrayParameterType::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        if ($locale !== null) {
            $query
                ->andWhere($query->expr()->eq('locale', ':locale'))
                ->setParameter('locale', $locale, Types::STRING);
        }

        $query->executeStatement();
    }

    /**
     * Loads all block collection IDs.
     *
     * @param int[] $blockIds
     *
     * @return int[]
     */
    public function loadBlockCollectionIds(array $blockIds, ?Status $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT bc.collection_id')
            ->from('nglayouts_block_collection', 'bc')
            ->where(
                $query->expr()->in('bc.block_id', [':block_id']),
            )
            ->setParameter('block_id', $blockIds, ArrayParameterType::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'bc.block_status', 'block_status');
        }

        $result = $query->fetchAllAssociative();

        return array_map('intval', array_column($result, 'collection_id'));
    }

    /**
     * Builds and returns a collection database SELECT query.
     */
    private function getCollectionSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT c.*, ct.*, bc.*')
            ->from('nglayouts_collection', 'c')
            ->innerJoin(
                'c',
                'nglayouts_collection_translation',
                'ct',
                $query->expr()->and(
                    $query->expr()->eq('ct.collection_id', 'c.id'),
                    $query->expr()->eq('ct.status', 'c.status'),
                ),
            )->innerJoin(
                'c',
                'nglayouts_block_collection',
                'bc',
                $query->expr()->and(
                    $query->expr()->eq('c.id', 'bc.collection_id'),
                    $query->expr()->eq('c.status', 'bc.collection_status'),
                ),
            );

        return $query;
    }

    /**
     * Builds and returns a collection database SELECT query.
     */
    private function getCollectionWithBlockSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT c.*, ct.*, b.id as block_id, b.uuid as block_uuid')
            ->from('nglayouts_collection', 'c')
            ->innerJoin(
                'c',
                'nglayouts_collection_translation',
                'ct',
                $query->expr()->and(
                    $query->expr()->eq('ct.collection_id', 'c.id'),
                    $query->expr()->eq('ct.status', 'c.status'),
                ),
            )->innerJoin(
                'c',
                'nglayouts_block_collection',
                'bc',
                $query->expr()->and(
                    $query->expr()->eq('c.id', 'bc.collection_id'),
                    $query->expr()->eq('c.status', 'bc.collection_status'),
                ),
            )->innerJoin(
                'bc',
                'nglayouts_block',
                'b',
                $query->expr()->and(
                    $query->expr()->eq('bc.block_id', 'b.id'),
                    $query->expr()->eq('bc.block_status', 'b.status'),
                ),
            );

        return $query;
    }

    /**
     * Builds and returns an item database SELECT query.
     */
    private function getItemSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT i.*, c.uuid AS collection_uuid')
            ->from('nglayouts_collection_item', 'i')
            ->innerJoin(
                'i',
                'nglayouts_collection',
                'c',
                $query->expr()->and(
                    $query->expr()->eq('c.id', 'i.collection_id'),
                    $query->expr()->eq('c.status', 'i.status'),
                ),
            );

        return $query;
    }

    /**
     * Builds and returns a block database SELECT query.
     */
    private function getQuerySelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT q.*, qt.*, c.uuid as collection_uuid')
            ->from('nglayouts_collection_query', 'q')
            ->innerJoin(
                'q',
                'nglayouts_collection_query_translation',
                'qt',
                $query->expr()->and(
                    $query->expr()->eq('qt.query_id', 'q.id'),
                    $query->expr()->eq('qt.status', 'q.status'),
                ),
            )->innerJoin(
                'q',
                'nglayouts_collection',
                'c',
                $query->expr()->and(
                    $query->expr()->eq('c.id', 'q.collection_id'),
                    $query->expr()->eq('c.status', 'q.status'),
                ),
            );

        return $query;
    }

    /**
     * Builds and returns a slot database SELECT query.
     */
    private function getSlotSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT s.*, c.uuid AS collection_uuid')
            ->from('nglayouts_collection_slot', 's')
            ->innerJoin(
                's',
                'nglayouts_collection',
                'c',
                $query->expr()->and(
                    $query->expr()->eq('c.id', 's.collection_id'),
                    $query->expr()->eq('c.status', 's.status'),
                ),
            );

        return $query;
    }
}
