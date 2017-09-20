<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

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
    public function loadBlockData($blockId, $status)
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('b.id', ':id')
        )
        ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'b.status');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all collection reference data.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $identifier
     *
     * @return array
     */
    public function loadCollectionReferencesData(Block $block, $identifier = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('block_id', 'block_status', 'collection_id', 'collection_status', 'identifier', 'start', 'length')
            ->from('ngbm_block_collection')
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

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all layout block data.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return array
     */
    public function loadLayoutBlocksData(Layout $layout)
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('b.layout_id', ':layout_id')
        )
        ->setParameter('layout_id', $layout->id, Type::INTEGER);

        $this->applyStatusCondition($query, $layout->status, 'b.status');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all zone block data.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return array
     */
    public function loadZoneBlocksData(Zone $zone)
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->like('b.path', ':path')
        )
        ->setParameter('path', '%/' . $zone->rootBlockId . '/%', Type::STRING);

        $this->applyStatusCondition($query, $zone->status, 'b.status');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all child block data from specified block, optionally filtered by placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $placeholder
     *
     * @return array
     */
    public function loadChildBlocksData(Block $block, $placeholder = null)
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('b.parent_id', ':parent_id')
        )
        ->setParameter('parent_id', $block->id, Type::INTEGER)
        ->addOrderBy('b.placeholder', 'ASC')
        ->addOrderBy('b.position', 'ASC');

        if ($placeholder !== null) {
            $query->andWhere(
                $query->expr()->eq('b.placeholder', ':placeholder')
            )
            ->setParameter('placeholder', $placeholder, Type::STRING);
        }

        $this->applyStatusCondition($query, $block->status, 'b.status');

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
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param bool $updatePath
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function createBlock(Block $block, $updatePath = true)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_block')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'layout_id' => ':layout_id',
                    'depth' => ':depth',
                    'path' => ':path',
                    'parent_id' => ':parent_id',
                    'placeholder' => ':placeholder',
                    'position' => ':position',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'item_view_type' => ':item_view_type',
                    'name' => ':name',
                    'translatable' => ':translatable',
                    'always_available' => ':always_available',
                    'main_locale' => ':main_locale',
                    'config' => ':config',
                )
            )
            ->setValue(
                'id',
                $block->id !== null ?
                    (int) $block->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_block')
            )
            ->setParameter('status', $block->status, Type::INTEGER)
            ->setParameter('layout_id', $block->layoutId, Type::INTEGER)
            ->setParameter('depth', $block->depth, Type::STRING)
            // Materialized path is updated after block is created
            ->setParameter('path', $block->path, Type::STRING)
            ->setParameter('parent_id', $block->parentId, Type::INTEGER)
            ->setParameter('placeholder', $block->placeholder, Type::STRING)
            ->setParameter('position', $block->position, Type::INTEGER)
            ->setParameter('definition_identifier', $block->definitionIdentifier, Type::STRING)
            ->setParameter('view_type', $block->viewType, Type::STRING)
            ->setParameter('item_view_type', $block->itemViewType, Type::STRING)
            ->setParameter('name', $block->name, Type::STRING)
            ->setParameter('translatable', $block->isTranslatable, Type::BOOLEAN)
            ->setParameter('always_available', $block->alwaysAvailable, Type::BOOLEAN)
            ->setParameter('main_locale', $block->mainLocale, Type::STRING)
            ->setParameter('config', $block->config, Type::JSON_ARRAY);

        $query->execute();

        if ($block->id === null) {
            $block->id = (int) $this->connectionHelper->lastInsertId('ngbm_block');
        }

        if (!$updatePath) {
            return $block;
        }

        // Update materialized path only after creating the block, when we have the ID

        $block->path = $block->path . $block->id . '/';

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('path', ':path')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $block->id, Type::INTEGER)
            ->setParameter('path', $block->path, Type::STRING);

        $this->applyStatusCondition($query, $block->status);

        $query->execute();

        return $block;
    }

    /**
     * Creates a block translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $locale
     */
    public function createBlockTranslation(Block $block, $locale)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_block_translation')
            ->values(
                array(
                    'block_id' => ':block_id',
                    'status' => ':status',
                    'locale' => ':locale',
                    'parameters' => ':parameters',
                )
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->setParameter('status', $block->status, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING)
            ->setParameter('parameters', $block->parameters[$locale], Type::JSON_ARRAY);

        $query->execute();
    }

    /**
     * Creates the collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReference $collectionReference
     */
    public function createCollectionReference(CollectionReference $collectionReference)
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
            ->setParameter('block_id', $collectionReference->blockId, Type::INTEGER)
            ->setParameter('block_status', $collectionReference->blockStatus, Type::INTEGER)
            ->setParameter('collection_id', $collectionReference->collectionId, Type::INTEGER)
            ->setParameter('collection_status', $collectionReference->collectionStatus, Type::INTEGER)
            ->setParameter('identifier', $collectionReference->identifier, Type::STRING)
            ->setParameter('start', $collectionReference->offset, Type::INTEGER)
            ->setParameter('length', $collectionReference->limit, Type::INTEGER);

        $query->execute();
    }

    /**
     * Updates a block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     */
    public function updateBlock(Block $block)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('layout_id', ':layout_id')
            ->set('depth', ':depth')
            ->set('path', ':path')
            ->set('parent_id', ':parent_id')
            ->set('placeholder', ':placeholder')
            ->set('position', ':position')
            ->set('definition_identifier', ':definition_identifier')
            ->set('view_type', ':view_type')
            ->set('item_view_type', ':item_view_type')
            ->set('name', ':name')
            ->set('translatable', ':translatable')
            ->set('main_locale', ':main_locale')
            ->set('always_available', ':always_available')
            ->set('config', ':config')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $block->id, Type::INTEGER)
            ->setParameter('layout_id', $block->layoutId, Type::INTEGER)
            ->setParameter('depth', $block->depth, Type::STRING)
            ->setParameter('path', $block->path, Type::STRING)
            ->setParameter('parent_id', $block->parentId, Type::INTEGER)
            ->setParameter('placeholder', $block->placeholder, Type::STRING)
            ->setParameter('position', $block->position, Type::INTEGER)
            ->setParameter('definition_identifier', $block->definitionIdentifier, Type::STRING)
            ->setParameter('view_type', $block->viewType, Type::STRING)
            ->setParameter('item_view_type', $block->itemViewType, Type::STRING)
            ->setParameter('name', $block->name, Type::STRING)
            ->setParameter('translatable', $block->isTranslatable, Type::BOOLEAN)
            ->setParameter('main_locale', $block->mainLocale, Type::STRING)
            ->setParameter('always_available', $block->alwaysAvailable, Type::BOOLEAN)
            ->setParameter('config', $block->config, Type::JSON_ARRAY);

        $this->applyStatusCondition($query, $block->status);

        $query->execute();
    }

    /**
     * Updates a block translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $locale
     */
    public function updateBlockTranslation(Block $block, $locale)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block_translation')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('locale', ':locale')
                )
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING)
            ->setParameter('parameters', $block->parameters[$locale], Type::JSON_ARRAY);

        $this->applyStatusCondition($query, $block->status);

        $query->execute();
    }

    /**
     * Updates a collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReference $collectionReference
     */
    public function updateCollectionReference(CollectionReference $collectionReference)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block_collection')
            ->set('collection_id', ':collection_id')
            ->set('collection_status', ':collection_status')
            ->set('start', ':start')
            ->set('length', ':length')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('block_status', ':block_status'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('block_id', $collectionReference->blockId, Type::INTEGER)
            ->setParameter('identifier', $collectionReference->identifier, Type::STRING)
            ->setParameter('collection_id', $collectionReference->collectionId, Type::INTEGER)
            ->setParameter('collection_status', $collectionReference->collectionStatus, Type::INTEGER)
            ->setParameter('start', $collectionReference->offset, Type::INTEGER)
            ->setParameter('length', $collectionReference->limit, Type::INTEGER);

        $this->applyStatusCondition($query, $collectionReference->blockStatus, 'block_status', 'block_status');

        $query->execute();
    }

    /**
     * Moves a block. If the target block is not provided, the block is only moved within its
     * current parent ID and placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     */
    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position)
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('position', ':position')
            ->set('parent_id', ':parent_id')
            ->set('placeholder', ':placeholder')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $block->id, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER)
            ->setParameter('parent_id', $targetBlock->id, Type::INTEGER)
            ->setParameter('placeholder', $placeholder, Type::STRING);

        $this->applyStatusCondition($query, $block->status);

        $query->execute();

        $depthDifference = $block->depth - ($targetBlock->depth + 1);

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('layout_id', ':layout_id')
            ->set('depth', 'depth - :depth_difference')
            ->set('path', 'replace(path, :old_path, :new_path)')
            ->where(
                $query->expr()->like('path', ':path')
            )
            ->setParameter('layout_id', $targetBlock->layoutId, Type::INTEGER)
            ->setParameter('depth_difference', $depthDifference, Type::INTEGER)
            ->setParameter('old_path', $block->path, Type::STRING)
            ->setParameter('new_path', $targetBlock->path . $block->id . '/', Type::STRING)
            ->setParameter('path', $block->path . '%', Type::STRING);

        $this->applyStatusCondition($query, $block->status);

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
     * Deletes block translations.
     *
     * @param array $blockIds
     * @param int $status
     * @param string $locale
     */
    public function deleteBlockTranslations(array $blockIds, $status = null, $locale = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block_translation')
            ->where(
                $query->expr()->in('block_id', array(':block_id'))
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY);

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
     * Loads all sub block IDs.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return array
     */
    public function loadSubBlockIds($blockId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('ngbm_block')
            ->where(
                $query->expr()->like('path', ':path')
            )
            ->setParameter('path', '%/' . (int) $blockId . '/%', Type::STRING);

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
            ->where(
                $query->expr()->in('bc.block_id', array(':block_id'))
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY);

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
    private function getBlockSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT b.*, bt.*')
            ->from('ngbm_block', 'b')
            ->innerJoin(
                'b',
                'ngbm_block_translation',
                'bt',
                $query->expr()->andX(
                    $query->expr()->eq('bt.block_id', 'b.id'),
                    $query->expr()->eq('bt.status', 'b.status')
                )
            );

        return $query;
    }
}
