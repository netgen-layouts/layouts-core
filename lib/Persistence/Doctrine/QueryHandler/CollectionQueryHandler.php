<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;

class CollectionQueryHandler extends QueryHandler
{
    /**
     * Loads all collection data for collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    public function loadCollectionData($collectionId, $status)
    {
        $query = $this->getCollectionSelectQuery();
        $query->where(
            $query->expr()->eq('c.id', ':id')
        )
        ->setParameter('id', $collectionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'c.status');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for an item.
     *
     * @param int|string $itemId
     * @param int $status
     *
     * @return array
     */
    public function loadItemData($itemId, $status)
    {
        $query = $this->getItemSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $itemId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for a query.
     *
     * @param int|string $queryId
     * @param int $status
     *
     * @return array
     */
    public function loadQueryData($queryId, $status)
    {
        $query = $this->getQuerySelectQuery();
        $query->where(
            $query->expr()->eq('q.id', ':id')
        )
        ->setParameter('id', $queryId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'q.status');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for items that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return array
     */
    public function loadCollectionItemsData(Collection $collection)
    {
        $query = $this->getItemSelectQuery();
        $query->where(
            $query->expr()->eq('collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collection->id, Type::INTEGER);

        $this->applyStatusCondition($query, $collection->status);

        $query->addOrderBy('position', 'ASC');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all collection query IDs.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    public function loadCollectionQueryIds($collectionId, $status = null)
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

        $result = $query->execute()->fetchAll();

        return array_map(
            function (array $row) {
                return $row['id'];
            },
            $result
        );
    }

    /**
     * Loads all data for queries that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return array
     */
    public function loadCollectionQueryData(Collection $collection)
    {
        $query = $this->getQuerySelectQuery();
        $query->where(
            $query->expr()->eq('q.collection_id', ':collection_id')
        )
        ->setParameter('collection_id', $collection->id, Type::INTEGER);

        $this->applyStatusCondition($query, $collection->status, 'q.status');

        return $query->execute()->fetchAll();
    }

    /**
     * Returns if the collection exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_collection')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collectionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(Collection $collection)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'translatable' => ':translatable',
                    'main_locale' => ':main_locale',
                    'always_available' => ':always_available',
                )
            )
            ->setValue(
                'id',
                $collection->id !== null ?
                    (int) $collection->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_collection')
            )
            ->setParameter('status', $collection->status, Type::INTEGER)
            ->setParameter('translatable', $collection->isTranslatable, Type::BOOLEAN)
            ->setParameter('main_locale', $collection->mainLocale, Type::STRING)
            ->setParameter('always_available', $collection->alwaysAvailable, Type::BOOLEAN);

        $query->execute();

        if ($collection->id === null) {
            $collection->id = (int) $this->connectionHelper->lastInsertId('ngbm_collection');
        }

        return $collection;
    }

    /**
     * Creates a collection translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $locale
     */
    public function createCollectionTranslation(Collection $collection, $locale)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_translation')
            ->values(
                array(
                    'collection_id' => ':collection_id',
                    'status' => ':status',
                    'locale' => ':locale',
                )
            )
            ->setParameter('collection_id', $collection->id, Type::INTEGER)
            ->setParameter('status', $collection->status, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING);

        $query->execute();
    }

    /**
     * Updates a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     */
    public function updateCollection(Collection $collection)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_collection')
            ->set('translatable', ':translatable')
            ->set('main_locale', ':main_locale')
            ->set('always_available', ':always_available')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $collection->id, Type::INTEGER)
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
    public function deleteCollection($collectionId, $status = null)
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

    /*
     * Deletes collection translations.
     *
     * @param int|string $collectionId
     * @param int $status
     * @param string $locale
     */
    public function deleteCollectionTranslations($collectionId, $status = null, $locale = null)
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
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem(Item $item)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_item')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'position' => ':position',
                    'type' => ':type',
                    'value_id' => ':value_id',
                    'value_type' => ':value_type',
                )
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
            ->setParameter('value_id', $item->valueId, Type::STRING)
            ->setParameter('value_type', $item->valueType, Type::STRING);

        $query->execute();

        if ($item->id === null) {
            $item->id = (int) $this->connectionHelper->lastInsertId('ngbm_collection_item');
        }

        return $item;
    }

    /**
     * Updates an item.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     */
    public function updateItem(Item $item)
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_collection_item')
            ->set('collection_id', ':collection_id')
            ->set('position', ':position')
            ->set('type', ':type')
            ->set('value_id', ':value_id')
            ->set('value_type', ':value_type')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $item->id, Type::INTEGER)
            ->setParameter('collection_id', $item->collectionId, Type::INTEGER)
            ->setParameter('position', $item->position, Type::INTEGER)
            ->setParameter('type', $item->type, Type::INTEGER)
            ->setParameter('value_id', $item->valueId, Type::STRING)
            ->setParameter('value_type', $item->valueType, Type::STRING);

        $this->applyStatusCondition($query, $item->status);

        $query->execute();
    }

    /**
     * Deletes an item.
     *
     * @param int|string $itemId
     * @param int $status
     */
    public function deleteItem($itemId, $status)
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
     * Deletes all collection items.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollectionItems($collectionId, $status = null)
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
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function createQuery(Query $query)
    {
        $dbQuery = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_query')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'type' => ':type',
                )
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

        if ($query->id === null) {
            $query->id = (int) $this->connectionHelper->lastInsertId('ngbm_collection_query');
        }

        return $query;
    }

    /**
     * Creates a query translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param string $locale
     */
    public function createQueryTranslation(Query $query, $locale)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_collection_query_translation')
            ->values(
                array(
                    'query_id' => ':query_id',
                    'status' => ':status',
                    'locale' => ':locale',
                    'parameters' => ':parameters',
                )
            )
            ->setParameter('query_id', $query->id, Type::INTEGER)
            ->setParameter('status', $query->status, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING)
            ->setParameter('parameters', $query->parameters[$locale], Type::JSON_ARRAY);

        $query->execute();
    }

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     */
    public function updateQuery(Query $query)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->update('ngbm_collection_query')
            ->set('collection_id', ':collection_id')
            ->set('type', ':type')
            ->where(
                $queryBuilder->expr()->eq('id', ':id')
            )
            ->setParameter('id', $query->id, Type::INTEGER)
            ->setParameter('collection_id', $query->collectionId, Type::INTEGER)
            ->setParameter('type', $query->type, Type::STRING);

        $this->applyStatusCondition($queryBuilder, $query->status);

        $queryBuilder->execute();
    }

    /**
     * Updates a query translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param string $locale
     */
    public function updateQueryTranslation(Query $query, $locale)
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
     *
     * @param array $queryIds
     * @param int $status
     */
    public function deleteQuery($queryIds, $status = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_query')
            ->where(
                $query->expr()->in('id', array(':query_id'))
            )
            ->setParameter('query_id', $queryIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes the query translations with provided query IDs.
     *
     * @param array $queryIds
     * @param int $status
     * @param string $locale
     */
    public function deleteQueryTranslations($queryIds, $status = null, $locale = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_collection_query_translation')
            ->where(
                $query->expr()->in('query_id', array(':query_id'))
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
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getCollectionSelectQuery()
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
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getItemSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT ngbm_collection_item.*')
            ->from('ngbm_collection_item');

        return $query;
    }

    /**
     * Builds and returns a block database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getQuerySelectQuery()
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
