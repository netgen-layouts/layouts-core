<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Netgen\BlockManager\Persistence\Values\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\BlockUpdateStruct;
use Doctrine\DBAL\Types\Type;

class BlockQueryHandler extends QueryHandler
{
    /**
     * Loads all block data.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return array
     */
    public function loadBlockData($blockId, $status = null)
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $blockId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all collection reference data.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     *
     * @return array
     */
    public function loadCollectionReferencesData($blockId, $status = null, $identifier = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('block_id', 'block_status', 'collection_id', 'collection_status', 'identifier', 'start', 'length')
            ->from('ngbm_block_collection')
            ->where(
                $query->expr()->eq('block_id', ':block_id')
            )
            ->setParameter('block_id', $blockId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'block_status');
            $query->addOrderBy('block_status', 'ASC');
        }

        if ($identifier !== null) {
            $query->andWhere($query->expr()->eq('identifier', ':identifier'))
                ->setParameter('identifier', $identifier, Type::STRING);
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all block data from specified zone.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return array
     */
    public function loadZoneBlocksData($layoutId, $zoneIdentifier, $status = null)
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING)
            ->orderBy('position', 'ASC');

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns if block exists.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return bool
     */
    public function blockExists($blockId, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_block')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\BlockCreateStruct $blockCreateStruct
     * @param int|string $blockId
     *
     * @return int
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, $blockId = null)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_block')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'layout_id' => ':layout_id',
                    'zone_identifier' => ':zone_identifier',
                    'position' => ':position',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'item_view_type' => ':item_view_type',
                    'name' => ':name',
                    'parameters' => ':parameters',
                )
            )
            ->setValue(
                'id',
                $blockId !== null ? (int)$blockId : $this->connectionHelper->getAutoIncrementValue('ngbm_block')
            )
            ->setParameter('status', $blockCreateStruct->status, Type::INTEGER)
            ->setParameter('layout_id', $blockCreateStruct->layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $blockCreateStruct->zoneIdentifier, Type::STRING)
            ->setParameter('position', $blockCreateStruct->position, Type::INTEGER)
            ->setParameter('definition_identifier', $blockCreateStruct->definitionIdentifier, Type::STRING)
            ->setParameter('view_type', $blockCreateStruct->viewType, Type::STRING)
            ->setParameter('item_view_type', $blockCreateStruct->itemViewType, Type::STRING)
            ->setParameter('name', trim($blockCreateStruct->name), Type::STRING)
            ->setParameter('parameters', $blockCreateStruct->parameters, is_array($blockCreateStruct->parameters) ? Type::JSON_ARRAY : Type::STRING);

        $query->execute();

        return (int)$this->connectionHelper->lastInsertId('ngbm_block');
    }

    /**
     * Updates a block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param \Netgen\BlockManager\Persistence\Values\BlockUpdateStruct $blockUpdateStruct
     */
    public function updateBlock($blockId, $status, BlockUpdateStruct $blockUpdateStruct)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('view_type', ':view_type')
            ->set('item_view_type', ':item_view_type')
            ->set('name', ':name')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER)
            ->setParameter('view_type', $blockUpdateStruct->viewType, Type::STRING)
            ->setParameter('item_view_type', $blockUpdateStruct->itemViewType, Type::STRING)
            ->setParameter('name', $blockUpdateStruct->name, Type::STRING)
            ->setParameter('parameters', $blockUpdateStruct->parameters, Type::JSON_ARRAY);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Updates a collection reference with specified identifier.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     * @param int|string $collectionId
     * @param int $collectionStatus
     */
    public function updateCollectionReference($blockId, $status, $identifier, $collectionId, $collectionStatus)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block_collection')
            ->set('collection_id', ':collection_id')
            ->set('collection_status', ':collection_status')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('block_status', ':block_status'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('collection_id', $collectionId, Type::INTEGER)
            ->setParameter('collection_status', $collectionStatus, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING);

        $this->applyStatusCondition($query, $status, 'block_status', 'block_status');

        $query->execute();
    }

    /**
     * Moves a block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $position
     * @param string $zoneIdentifier
     */
    public function moveBlock($blockId, $status, $position, $zoneIdentifier = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        if ($zoneIdentifier !== null) {
            $query
                ->set('zone_identifier', ':zone_identifier')
                ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING);
        }

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Deletes a block.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlock($blockId, $status)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Returns if provided collection identifier already exists in the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function collectionIdentifierExists($blockId, $status, $identifier)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_block_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING);

        $this->applyStatusCondition($query, $status, 'block_status');

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if provided collection already exists in the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int|string $collectionId
     * @param int $collectionStatus
     *
     * @return bool
     */
    public function collectionExists($blockId, $status, $collectionId, $collectionStatus)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_block_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('collection_id', ':collection_id')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('collection_id', $collectionId, Type::STRING);

        $this->applyStatusCondition($query, $status, 'block_status', 'block_status');
        $this->applyStatusCondition($query, $collectionStatus, 'collection_status', 'collection_status');

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Adds the collection to the block.
     *
     * @param int|string $blockId
     * @param int $blockStatus
     * @param int|string $collectionId
     * @param int $collectionStatus
     * @param string $identifier
     * @param int $offset
     * @param int $limit
     */
    public function addCollectionToBlock($blockId, $blockStatus, $collectionId, $collectionStatus, $identifier, $offset = 0, $limit = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query->insert('ngbm_block_collection')
            ->values(
                array(
                    'block_id' => ':block_id',
                    'block_status' => ':block_status',
                    'collection_id' => ':collection_id',
                    'collection_status' => ':collection_status',
                    'identifier' => ':identifier',
                    'start' => ':start',
                    'length' => ':length',
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('block_status', $blockStatus, Type::INTEGER)
            ->setParameter('collection_id', $collectionId, Type::INTEGER)
            ->setParameter('collection_status', $collectionStatus, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING)
            ->setParameter('start', $offset, Type::INTEGER)
            ->setParameter('length', $limit, Type::INTEGER);

        $query->execute();
    }

    /**
     * Removes the collection from the block.
     *
     * @param int|string $blockId
     * @param int $blockStatus
     * @param int|string $collectionId
     * @param int $collectionStatus
     */
    public function removeCollectionFromBlock($blockId, $blockStatus, $collectionId, $collectionStatus)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('collection_id', ':collection_id')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        $this->applyStatusCondition($query, $blockStatus, 'block_status', 'block_status');
        $this->applyStatusCondition($query, $collectionStatus, 'collection_status', 'collection_status');

        $query->execute();
    }

    /**
     * Builds and returns a block database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBlockSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id', 'status', 'layout_id', 'zone_identifier', 'position', 'definition_identifier', 'view_type', 'item_view_type', 'name', 'parameters')
            ->from('ngbm_block');

        return $query;
    }
}
