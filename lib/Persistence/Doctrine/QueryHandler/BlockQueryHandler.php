<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use PDO;

final class BlockQueryHandler extends QueryHandler
{
    /**
     * Loads all block data.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return array
     */
    public function loadBlockData($blockId, int $status): array
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('b.id', ':id')
        )
        ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'b.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all collection reference data.
     */
    public function loadCollectionReferencesData(Block $block, ?string $identifier = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('block_id', 'block_status', 'collection_id', 'collection_status', 'identifier')
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all layout block data.
     */
    public function loadLayoutBlocksData(Layout $layout): array
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('b.layout_id', ':layout_id')
        )
        ->setParameter('layout_id', $layout->id, Type::INTEGER);

        $this->applyStatusCondition($query, $layout->status, 'b.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all zone block data.
     */
    public function loadZoneBlocksData(Zone $zone): array
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->like('b.path', ':path')
        )
        ->setParameter('path', '%/' . $zone->rootBlockId . '/%', Type::STRING);

        $this->applyStatusCondition($query, $zone->status, 'b.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads all child block data from specified block, optionally filtered by placeholder.
     */
    public function loadChildBlocksData(Block $block, ?string $placeholder = null): array
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns if block exists.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return bool
     */
    public function blockExists($blockId, int $status): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_block')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Creates a block.
     */
    public function createBlock(Block $block, bool $updatePath = true): Block
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_block')
            ->values(
                [
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
                ]
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

        $block->id = $block->id ?? (int) $this->connectionHelper->lastInsertId('ngbm_block');

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
     */
    public function createBlockTranslation(Block $block, string $locale): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_block_translation')
            ->values(
                [
                    'block_id' => ':block_id',
                    'status' => ':status',
                    'locale' => ':locale',
                    'parameters' => ':parameters',
                ]
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->setParameter('status', $block->status, Type::INTEGER)
            ->setParameter('locale', $locale, Type::STRING)
            ->setParameter('parameters', $block->parameters[$locale], Type::JSON_ARRAY);

        $query->execute();
    }

    /**
     * Creates the collection reference.
     */
    public function createCollectionReference(CollectionReference $collectionReference): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->insert('ngbm_block_collection')
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
     * Updates a block.
     */
    public function updateBlock(Block $block): void
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
     */
    public function updateBlockTranslation(Block $block, string $locale): void
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
     * Moves a block. If the target block is not provided, the block is only moved within its
     * current parent ID and placeholder.
     */
    public function moveBlock(Block $block, Block $targetBlock, string $placeholder, int $position): void
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
     */
    public function deleteBlocks(array $blockIds, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block')
            ->where(
                $query->expr()->in('id', [':id'])
            )
            ->setParameter('id', $blockIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes block translations.
     */
    public function deleteBlockTranslations(array $blockIds, ?int $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block_translation')
            ->where(
                $query->expr()->in('block_id', [':block_id'])
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
     */
    public function deleteCollectionReferences(array $blockIds, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block_collection')
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
     * Loads all sub block IDs.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return array
     */
    public function loadSubBlockIds($blockId, ?int $status = null): array
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

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_column($result, 'id');
    }

    /**
     * Loads all layout block IDs.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    public function loadLayoutBlockIds($layoutId, ?int $status = null): array
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

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_column($result, 'id');
    }

    /**
     * Loads all block collection IDs.
     */
    public function loadBlockCollectionIds(array $blockIds, ?int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT bc.collection_id')
            ->from('ngbm_block_collection', 'bc')
            ->where(
                $query->expr()->in('bc.block_id', [':block_id'])
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'bc.block_status', 'block_status');
        }

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_column($result, 'collection_id');
    }

    /**
     * Builds and returns a block database SELECT query.
     */
    private function getBlockSelectQuery(): QueryBuilder
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
