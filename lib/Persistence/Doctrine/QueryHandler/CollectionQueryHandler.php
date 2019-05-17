<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use PDO;

final class CollectionQueryHandler extends QueryHandler
{
    /**
     * Loads all collection data for collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    public function loadCollectionData($collectionId, int $status): array
    {
        $query = $this->getCollectionSelectQuery();

        $this->applyIdCondition($query, $collectionId, 'c.id', 'c.uuid');
        $this->applyStatusCondition($query, $status, 'c.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all collection reference data.
     */
    public function loadCollectionReferencesData(Block $block, ?string $identifier = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('block_id', 'block_status', 'collection_id', 'collection_status', 'identifier')
            ->from('nglayouts_block_collection')
            ->where(
                $query->expr()->eq('block_id', ':block_id')
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->orderBy('identifier', 'ASC');

        $this->applyStatusCondition($query, $block->status, 'block_status');

        if ($identifier !== null) {
            $query->andWhere($query->expr()->eq('identifier', ':identifier'))
                ->setParameter('identifier', $identifier, Type::STRING);
        }

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all data for an item.
     *
     * @param int|string $itemId
     * @param int $status
     *
     * @return array
     */
    public function loadItemData($itemId, int $status): array
    {
        $query = $this->getItemSelectQuery();

        $this->applyIdCondition($query, $itemId, 'i.id', 'i.uuid');
        $this->applyStatusCondition($query, $status, 'i.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads an item with specified position in specified collection.
     */
    public function loadItemWithPositionData(Collection $collection, int $position): array
    {
        $query = $this->getItemSelectQuery();
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq('i.collection_id', ':collection_id'),
                $query->expr()->eq('i.position', ':position')
            )
        )
        ->setParameter('collection_id', $collection->id, Type::INTEGER)
        ->setParameter('position', $position, Type::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 'i.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all data for a query.
     *
     * @param int|string $queryId
     * @param int $status
     *
     * @return array
     */
    public function loadQueryData($queryId, int $status): array
    {
        $query = $this->getQuerySelectQuery();

        $this->applyIdCondition($query, $queryId, 'q.id', 'q.uuid');
        $this->applyStatusCondition($query, $status, 'q.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all data for items that belong to collection with specified ID.
     */
    public function loadCollectionItemsData(Collection $collection): array
    {
        $query = $this->getItemSelectQuery();
        $query->where(
            $query->expr()->eq('i.collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collection->id, Type::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 'i.status');

        $query->addOrderBy('i.position', 'ASC');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all collection query IDs.
     */
    public function loadCollectionQueryIds(int $collectionId, ?int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_collection_query')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_map('intval', array_column($result, 'id'));
    }

    /**
     * Loads all data for queries that belong to collection with specified ID.
     */
    public function loadCollectionQueryData(Collection $collection): array
    {
        $query = $this->getQuerySelectQuery();
        $query->where(
            $query->expr()->eq('q.collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collection->id, Type::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 'q.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns if the collection exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, int $status): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_collection');

        $this->applyIdCondition($query, $collectionId);
        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

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
                ]
            )
            ->setValue('id', $collection->id ?? $this->connectionHelper->getAutoIncrementValue('nglayouts_collection'))
            ->setParameter('uuid', $collection->uuid, Type::STRING)
            ->setParameter('status', $collection->status, Type::INTEGER)
            ->setParameter('start', $collection->offset, Type::INTEGER)
            ->setParameter('length', $collection->limit, Type::INTEGER)
            ->setParameter('translatable', $collection->isTranslatable, Type::BOOLEAN)
            ->setParameter('main_locale', $collection->mainLocale, Type::STRING)
            ->setParameter('always_available', $collection->alwaysAvailable, Type::BOOLEAN);

        $query->execute();

        $collection->id = $collection->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_collection');

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
                ]
            )
            ->setParameter('collection_id', $collection->id, Type::INTEGER)
            ->setParameter('status', $collection->status, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING);

        $query->execute();
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
                ]
            )
            ->setParameter('block_id', $collectionReference->blockId, Type::INTEGER)
            ->setParameter('block_status', $collectionReference->blockStatus, Type::INTEGER)
            ->setParameter('collection_id', $collectionReference->collectionId, Type::INTEGER)
            ->setParameter('collection_status', $collectionReference->collectionStatus, Type::INTEGER)
            ->setParameter('identifier', $collectionReference->identifier, Type::STRING);

        $query->execute();
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
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collection->id, Type::INTEGER)
            ->setParameter('uuid', $collection->uuid, Type::STRING)
            ->setParameter('start', $collection->offset, Type::INTEGER)
            ->setParameter('length', $collection->limit, Type::INTEGER)
            ->setParameter('translatable', $collection->isTranslatable, Type::BOOLEAN)
            ->setParameter('main_locale', $collection->mainLocale, Type::STRING)
            ->setParameter('always_available', $collection->alwaysAvailable, Type::BOOLEAN);

        $this->applyStatusCondition($query, $collection->status);

        $query->execute();
    }

    /**
     * Deletes a collection.
     */
    public function deleteCollection(int $collectionId, ?int $status = null): void
    {
        // Delete all connections between blocks and collections

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_block_collection')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'collection_status');
        }

        $query->execute();

        // Then delete the collection itself

        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_collection')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes collection translations.
     */
    public function deleteCollectionTranslations(int $collectionId, ?int $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_translation')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

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
     * Deletes the collection reference.
     */
    public function deleteCollectionReferences(array $blockIds, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_block_collection')
            ->where(
                $query->expr()->in('block_id', [':block_id'])
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'block_status', 'block_status');
        }

        $query->execute();
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
                    'config' => ':config',
                ]
            )
            ->setValue('id', $item->id ?? $this->connectionHelper->getAutoIncrementValue('nglayouts_collection_item'))
            ->setParameter('uuid', $item->uuid, Type::STRING)
            ->setParameter('status', $item->status, Type::INTEGER)
            ->setParameter('collection_id', $item->collectionId, Type::INTEGER)
            ->setParameter('position', $item->position, Type::INTEGER)
            ->setParameter('value', $item->value, Type::STRING)
            ->setParameter('value_type', $item->valueType, Type::STRING)
            ->setParameter('config', $item->config, Type::JSON_ARRAY);

        $query->execute();

        $item->id = $item->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_collection_item');

        return $item;
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
            ->set('config', ':config')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $item->id, Type::INTEGER)
            ->setParameter('uuid', $item->uuid, Type::STRING)
            ->setParameter('collection_id', $item->collectionId, Type::INTEGER)
            ->setParameter('position', $item->position, Type::INTEGER)
            ->setParameter('value', $item->value, Type::STRING)
            ->setParameter('value_type', $item->valueType, Type::STRING)
            ->setParameter('config', $item->config, Type::JSON_ARRAY);

        $this->applyStatusCondition($query, $item->status);

        $query->execute();
    }

    /**
     * Deletes an item.
     */
    public function deleteItem(int $itemId, int $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_item')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $itemId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Deletes all manual and override items from provided collection.
     *
     * If item type (one of Item::TYPE_* constants) is provided, only items
     * of that type are removed (manual or override).
     */
    public function deleteItems(int $collectionId, int $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_item')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Deletes all collection items.
     */
    public function deleteCollectionItems(int $collectionId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_collection_item')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
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
                ]
            )
            ->setValue('id', $query->id ?? $this->connectionHelper->getAutoIncrementValue('nglayouts_collection_query'))
            ->setParameter('uuid', $query->uuid, Type::STRING)
            ->setParameter('status', $query->status, Type::INTEGER)
            ->setParameter('collection_id', $query->collectionId, Type::INTEGER)
            ->setParameter('type', $query->type, Type::STRING);

        $dbQuery->execute();

        $query->id = $query->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_collection_query');

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
                ]
            )
            ->setParameter('query_id', $query->id, Type::INTEGER)
            ->setParameter('status', $query->status, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING)
            ->setParameter('parameters', $query->parameters[$locale], Type::JSON_ARRAY);

        $dbQuery->execute();
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
                $dbQuery->expr()->andX(
                    $dbQuery->expr()->eq('query_id', ':query_id'),
                    $dbQuery->expr()->eq('locale', ':locale')
                )
            )
            ->setParameter('query_id', $query->id, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING)
            ->setParameter('parameters', $query->parameters[$locale], Type::JSON_ARRAY);

        $this->applyStatusCondition($dbQuery, $query->status);

        $dbQuery->execute();
    }

    /**
     * Deletes the queries with provided IDs.
     */
    public function deleteQuery(array $queryIds, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_query')
            ->where(
                $query->expr()->in('id', [':query_id'])
            )
            ->setParameter('query_id', $queryIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes the query translations with provided query IDs.
     */
    public function deleteQueryTranslations(array $queryIds, ?int $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_collection_query_translation')
            ->where(
                $query->expr()->in('query_id', [':query_id'])
            )
            ->setParameter('query_id', $queryIds, Connection::PARAM_INT_ARRAY);

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
     * Loads all block collection IDs.
     */
    public function loadBlockCollectionIds(array $blockIds, ?int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT bc.collection_id')
            ->from('nglayouts_block_collection', 'bc')
            ->where(
                $query->expr()->in('bc.block_id', [':block_id'])
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'bc.block_status', 'block_status');
        }

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_map('intval', array_column($result, 'collection_id'));
    }

    /**
     * Builds and returns a collection database SELECT query.
     */
    private function getCollectionSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT c.*, ct.*')
            ->from('nglayouts_collection', 'c')
            ->innerJoin(
                'c',
                'nglayouts_collection_translation',
                'ct',
                $query->expr()->andX(
                    $query->expr()->eq('ct.collection_id', 'c.id'),
                    $query->expr()->eq('ct.status', 'c.status')
                )
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
                $query->expr()->andX(
                    $query->expr()->eq('c.id', 'i.collection_id'),
                    $query->expr()->eq('c.status', 'i.status')
                )
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
                $query->expr()->andX(
                    $query->expr()->eq('qt.query_id', 'q.id'),
                    $query->expr()->eq('qt.status', 'q.status')
                )
            )->innerJoin(
                'q',
                'nglayouts_collection',
                'c',
                $query->expr()->andX(
                    $query->expr()->eq('c.id', 'q.collection_id'),
                    $query->expr()->eq('c.status', 'q.status')
                )
            );

        return $query;
    }
}
