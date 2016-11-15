<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Netgen\BlockManager\Persistence\Values\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\BlockUpdateStruct;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Values\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\CollectionReferenceUpdateStruct;

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
            ->setParameter('parameters', $blockCreateStruct->parameters, Type::JSON_ARRAY);

        $query->execute();

        return (int)$this->connectionHelper->lastInsertId('ngbm_block');
    }

    /**
     * Creates the collection reference.
     *
     * @param int|string $blockId
     * @param int $blockStatus
     * @param \Netgen\BlockManager\Persistence\Values\CollectionReferenceCreateStruct $createStruct
     */
    public function createCollectionReference($blockId, $blockStatus, CollectionReferenceCreateStruct $createStruct)
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
            ->setParameter('collection_id', $createStruct->collection->id, Type::INTEGER)
            ->setParameter('collection_status', $createStruct->collection->status, Type::INTEGER)
            ->setParameter('identifier', $createStruct->identifier, Type::STRING)
            ->setParameter('start', $createStruct->offset, Type::INTEGER)
            ->setParameter('length', $createStruct->limit, Type::INTEGER);

        $query->execute();
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
     * @param \Netgen\BlockManager\Persistence\Values\CollectionReferenceUpdateStruct $updateStruct
     */
    public function updateCollectionReference($blockId, $status, $identifier, CollectionReferenceUpdateStruct $updateStruct)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block_collection')
            ->set('start', ':start')
            ->set('length', ':length')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('block_status', ':block_status'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING)
            ->setParameter('start', $updateStruct->offset, Type::INTEGER)
            ->setParameter('length', $updateStruct->limit, Type::INTEGER);

        if ($updateStruct->collection !== null) {
            $query
                ->set('collection_id', ':collection_id')
                ->set('collection_status', ':collection_status')
                ->setParameter('collection_id', $updateStruct->collection->id, Type::INTEGER)
                ->setParameter('collection_status', $updateStruct->collection->status, Type::INTEGER);
        }

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
     * Deletes all blocks with provided IDs.
     *
     * @param array $blockIds
     * @param int $status
     */
    public function deleteBlocks(array $blockIds, $status = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block')
            ->where(
                $query->expr()->in('id', array(':id'))
            )
            ->setParameter('id', $blockIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes the collection reference.
     *
     * @param int|string $blockId
     * @param int $blockStatus
     * @param string $identifier
     */
    public function deleteCollectionReference($blockId, $blockStatus, $identifier)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING);

        $this->applyStatusCondition($query, $blockStatus, 'block_status', 'block_status');

        $query->execute();
    }

    /**
     * Deletes the collection reference.
     *
     * @param array $blockIds
     * @param int $status
     */
    public function deleteCollectionReferences(array $blockIds, $status = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block_collection')
            ->where(
                $query->expr()->in('block_id', array(':block_id'))
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'block_status', 'block_status');
        }

        $query->execute();
    }

    /**
     * Loads all block collection IDs.
     *
     * @param array $blockIds
     * @param int $status
     *
     * @return array
     */
    public function loadBlockCollectionIds(array $blockIds, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT bc.collection_id')
            ->from('ngbm_block_collection', 'bc')
            ->innerJoin(
                'bc',
                'ngbm_collection',
                'c',
                $query->expr()->andX(
                    $query->expr()->eq('bc.collection_id', 'c.id'),
                    $query->expr()->eq('bc.collection_status', 'c.status'),
                    $query->expr()->eq('c.shared', ':shared')
                )
            )
            ->where(
                $query->expr()->in('bc.block_id', array(':block_id'))
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('shared', false, Type::BOOLEAN);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'bc.block_status', 'block_status');
        }

        $result = $query->execute()->fetchAll();

        return array_map(
            function (array $row) {
                return $row['collection_id'];
            },
            $result
        );
    }

    /**
     * Builds and returns a block database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBlockSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT ngbm_block.*')
            ->from('ngbm_block');

        return $query;
    }
}
