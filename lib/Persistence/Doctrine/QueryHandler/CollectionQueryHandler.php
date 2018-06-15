<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
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
        $query->where(
            $query->expr()->eq('c.id', ':id')
        )
        ->setParameter('id', $collectionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'c.status');

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
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $itemId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

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
                $query->expr()->eq('collection_id', ':collection_id'),
                $query->expr()->eq('position', ':position')
            )
        )
        ->setParameter('collection_id', $collection->id, Type::INTEGER)
        ->setParameter('position', $position, Type::INTEGER);

        $this->applyStatusCondition($query, $collection->status);

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
        $query->where(
            $query->expr()->eq('q.id', ':id')
        )
        ->setParameter('id', $queryId, Type::INTEGER);

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
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collection->id, Type::INTEGER);

        $this->applyStatusCondition($query, $collection->status);

        $query->addOrderBy('position', 'ASC');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all collection query IDs.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    public function loadCollectionQueryIds($collectionId, int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('ngbm_collection_query')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            function (array $row) {
                return $row['id'];
            },
            $result
        );
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
            ->from('ngbm_collection')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER);

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
            ->insert('ngbm_collection')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'start' => ':start',
                    'length' => ':length',
                    'translatable' => ':translatable',
                    'main_locale' => ':main_locale',
                    'always_available' => ':always_available',
                ]
            )
            ->setValue(
                'id',
                $collection->id !== null ?
                    (int) $collection->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_collection')
            )
            ->setParameter('status', $collection->status, Type::INTEGER)
            ->setParameter('start', $collection->offset, Type::INTEGER)
            ->setParameter('length', $collection->limit, Type::INTEGER)
            ->setParameter('translatable', $collection->isTranslatable, Type::BOOLEAN)
            ->setParameter('main_locale', $collection->mainLocale, Type::STRING)
            ->setParameter('always_available', $collection->alwaysAvailable, Type::BOOLEAN);

        $query->execute();

        $collection->id = $collection->id ?? (int) $this->connectionHelper->lastInsertId('ngbm_collection');

        return $collection;
    }

    /**
     * Creates a collection translation.
     */
    public function createCollectionTranslation(Collection $collection, string $locale): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_translation')
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
     * Updates a collection.
     */
    public function updateCollection(Collection $collection): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_collection')
            ->set('start', ':start')
            ->set('length', ':length')
            ->set('translatable', ':translatable')
            ->set('main_locale', ':main_locale')
            ->set('always_available', ':always_available')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collection->id, Type::INTEGER)
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
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollection($collectionId, int $status = null): void
    {
        // Delete all connections between blocks and collections

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_block_collection')
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
        $query->delete('ngbm_collection')
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
     *
     * @param int|string $collectionId
     * @param int $status
     * @param string $locale
     */
    public function deleteCollectionTranslations($collectionId, int $status = null, string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_translation')
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
     * Adds an item.
     */
    public function addItem(Item $item): Item
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_item')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'position' => ':position',
                    'type' => ':type',
                    'value' => ':value',
                    'value_type' => ':value_type',
                    'config' => ':config',
                ]
            )
            ->setValue(
                'id',
                $item->id !== null ?
                    (int) $item->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_collection_item')
            )
            ->setParameter('status', $item->status, Type::INTEGER)
            ->setParameter('collection_id', $item->collectionId, Type::INTEGER)
            ->setParameter('position', $item->position, Type::INTEGER)
            ->setParameter('type', $item->type, Type::INTEGER)
            ->setParameter('value', $item->value, Type::STRING)
            ->setParameter('value_type', $item->valueType, Type::STRING)
            ->setParameter('config', $item->config, Type::JSON_ARRAY);

        $query->execute();

        $item->id = $item->id ?? (int) $this->connectionHelper->lastInsertId('ngbm_collection_item');

        return $item;
    }

    /**
     * Updates an item.
     */
    public function updateItem(Item $item): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_collection_item')
            ->set('collection_id', ':collection_id')
            ->set('position', ':position')
            ->set('type', ':type')
            ->set('value', ':value')
            ->set('value_type', ':value_type')
            ->set('config', ':config')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $item->id, Type::INTEGER)
            ->setParameter('collection_id', $item->collectionId, Type::INTEGER)
            ->setParameter('position', $item->position, Type::INTEGER)
            ->setParameter('type', $item->type, Type::INTEGER)
            ->setParameter('value', $item->value, Type::STRING)
            ->setParameter('value_type', $item->valueType, Type::STRING)
            ->setParameter('config', $item->config, Type::JSON_ARRAY);

        $this->applyStatusCondition($query, $item->status);

        $query->execute();
    }

    /**
     * Deletes an item.
     *
     * @param int|string $itemId
     * @param int $status
     */
    public function deleteItem($itemId, int $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_item')
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
     *
     * @param int|string $collectionId
     * @param int $status
     * @param int $itemType
     */
    public function deleteItems($collectionId, int $status, int $itemType = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_item')
            ->where(
                $query->expr()->eq('collection_id', ':collection_id')
            )
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        if ($itemType !== null) {
            $query->andWhere(
                $query->expr()->eq('type', ':type')
            )
            ->setParameter('type', $itemType, Type::INTEGER);
        }

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Deletes all collection items.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollectionItems($collectionId, int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_collection_item')
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
            ->insert('ngbm_collection_query')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'type' => ':type',
                ]
            )
            ->setValue(
                'id',
                $query->id !== null ?
                    (int) $query->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_collection_query')
            )
            ->setParameter('status', $query->status, Type::INTEGER)
            ->setParameter('collection_id', $query->collectionId, Type::INTEGER)
            ->setParameter('type', $query->type, Type::STRING);

        $dbQuery->execute();

        $query->id = $query->id ?? (int) $this->connectionHelper->lastInsertId('ngbm_collection_query');

        return $query;
    }

    /**
     * Creates a query translation.
     */
    public function createQueryTranslation(Query $query, string $locale): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_query_translation')
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

        $query->execute();
    }

    /**
     * Updates a query translation.
     */
    public function updateQueryTranslation(Query $query, string $locale): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->update('ngbm_collection_query_translation')
            ->set('parameters', ':parameters')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('query_id', ':query_id'),
                    $queryBuilder->expr()->eq('locale', ':locale')
                )
            )
            ->setParameter('query_id', $query->id, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING)
            ->setParameter('parameters', $query->parameters[$locale], Type::JSON_ARRAY);

        $this->applyStatusCondition($queryBuilder, $query->status);

        $queryBuilder->execute();
    }

    /**
     * Deletes the queries with provided IDs.
     */
    public function deleteQuery(array $queryIds, int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_query')
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
    public function deleteQueryTranslations(array $queryIds, int $status = null, string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_query_translation')
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
     * Builds and returns a collection database SELECT query.
     */
    private function getCollectionSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT c.*, ct.*')
            ->from('ngbm_collection', 'c')
            ->innerJoin(
                'c',
                'ngbm_collection_translation',
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
        $query->select('DISTINCT ngbm_collection_item.*')
            ->from('ngbm_collection_item');

        return $query;
    }

    /**
     * Builds and returns a block database SELECT query.
     */
    private function getQuerySelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT q.*, qt.*')
            ->from('ngbm_collection_query', 'q')
            ->innerJoin(
                'q',
                'ngbm_collection_query_translation',
                'qt',
                $query->expr()->andX(
                    $query->expr()->eq('qt.query_id', 'q.id'),
                    $query->expr()->eq('qt.status', 'q.status')
                )
            );

        return $query;
    }
}
