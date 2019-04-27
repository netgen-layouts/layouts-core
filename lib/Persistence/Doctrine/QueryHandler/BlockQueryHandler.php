<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
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
        $query = $this->getBlockWithLayoutSelectQuery();

        $this->applyIdCondition($query, $blockId, 'b.id', 'b.uuid');
        $this->applyStatusCondition($query, $status, 'b.status');

        $blocksData = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        // Inject the parent UUID into the result
        // This is to avoid inner joining the block table with itself

        if (count($blocksData) > 0 && $blocksData[0]['parent_id'] > 0) {
            $parentUuid = $this->getBlockUuid((int) $blocksData[0]['parent_id']);
            if ($parentUuid === null) {
                // Having a parent ID, but not being able to find the UUID should not happen.
                // If it does, return any empty array as if the block with provided ID and status
                // does not exist.
                return [];
            }

            foreach ($blocksData as &$blockData) {
                $blockData['parent_uuid'] = $parentUuid;
            }
        }

        return $blocksData;
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

        $blocksData = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        // Map block IDs to UUIDs to inject parent UUID into the result
        // This is to avoid inner joining the block table with itself

        $idToUuidMap = [];

        foreach ($blocksData as $blockData) {
            $idToUuidMap[(int) $blockData['id']] = $blockData['uuid'];
        }

        foreach ($blocksData as &$blockData) {
            $parentId = $blockData['parent_id'] > 0 ? (int) $blockData['parent_id'] : null;
            $parentUuid = $parentId !== null ? ($idToUuidMap[$parentId] ?? null) : null;
            $blockData['parent_uuid'] = $parentUuid;
        }

        return $blocksData;
    }

    /**
     * Loads all child block data from specified block.
     *
     * This method returns the complete tree of blocks under the specified block.
     */
    public function loadAllChildBlocksData(Block $block): array
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->like('b.path', ':path')
        )
        ->setParameter('path', '%/' . $block->id . '/%', Type::STRING);

        $this->applyStatusCondition($query, $block->status, 'b.status');

        $blocksData = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        // Map block IDs to UUIDs to inject parent UUID into the result
        // This is to avoid inner joining the block table with itself

        $idToUuidMap = [$block->id => $block->uuid];

        foreach ($blocksData as $blockData) {
            $idToUuidMap[(int) $blockData['id']] = $blockData['uuid'];
        }

        foreach ($blocksData as &$blockData) {
            $parentId = $blockData['parent_id'] > 0 ? (int) $blockData['parent_id'] : null;
            $parentUuid = $parentId !== null ? ($idToUuidMap[$parentId] ?? null) : null;
            $blockData['parent_uuid'] = $parentUuid;
        }

        return $blocksData;
    }

    /**
     * Loads child block data from specified block, optionally filtered by placeholder.
     *
     * This method return only the first level of blocks under the specified block.
     */
    public function loadChildBlocksData(Block $block, ?string $placeholder = null): array
    {
        $query = $this->getBlockWithLayoutSelectQuery();
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

        $blocksData = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        // Map block IDs to UUIDs to inject parent UUID into the result
        // This is to avoid inner joining the block table with itself

        $idToUuidMap = [$block->id => $block->uuid];

        foreach ($blocksData as $blockData) {
            $idToUuidMap[(int) $blockData['id']] = $blockData['uuid'];
        }

        foreach ($blocksData as &$blockData) {
            $parentId = $blockData['parent_id'] > 0 ? (int) $blockData['parent_id'] : null;
            $parentUuid = $parentId !== null ? ($idToUuidMap[$parentId] ?? null) : null;
            $blockData['parent_uuid'] = $parentUuid;
        }

        return $blocksData;
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
            ->from('nglayouts_block');

        $this->applyIdCondition($query, $blockId);
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
            ->insert('nglayouts_block')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'uuid' => ':uuid',
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
            ->setValue('id', $block->id ?? $this->connectionHelper->getAutoIncrementValue('nglayouts_block'))
            ->setParameter('status', $block->status, Type::INTEGER)
            ->setParameter('uuid', $block->uuid, Type::STRING)
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

        $block->id = $block->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_block');

        if (!$updatePath) {
            return $block;
        }

        // Update materialized path only after creating the block, when we have the ID

        $block->path = $block->path . $block->id . '/';

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_block')
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
            ->insert('nglayouts_block_translation')
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
     * Updates a block.
     */
    public function updateBlock(Block $block): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_block')
            ->set('uuid', ':uuid')
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
            ->setParameter('uuid', $block->uuid, Type::STRING)
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
            ->update('nglayouts_block_translation')
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
     * current parent block and placeholder.
     */
    public function moveBlock(Block $block, Block $targetBlock, string $placeholder, int $position): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('nglayouts_block')
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
            ->update('nglayouts_block')
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

        $query->delete('nglayouts_block')
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

        $query->delete('nglayouts_block_translation')
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
     * Loads all sub block IDs.
     */
    public function loadSubBlockIds(int $blockId, ?int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_block')
            ->where(
                $query->expr()->like('path', ':path')
            )
            ->setParameter('path', '%/' . $blockId . '/%', Type::STRING);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_map('intval', array_column($result, 'id'));
    }

    /**
     * Loads all layout block IDs.
     */
    public function loadLayoutBlockIds(int $layoutId, ?int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_map('intval', array_column($result, 'id'));
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
     * Returns the block UUID for provided block ID.
     *
     * If block with provided ID does not exist, null is returned.
     */
    private function getBlockUuid(int $blockId): ?string
    {
        $query = $this->connection->createQueryBuilder();

        $query->select('b.uuid')
            ->from('nglayouts_block', 'b')
            ->where(
                $query->expr()->eq('b.id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (count($data) === 0) {
            return null;
        }

        return $data[0]['uuid'];
    }

    /**
     * Builds and returns a block database SELECT query.
     */
    private function getBlockSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT b.*, bt.*')
            ->from('nglayouts_block', 'b')
            ->innerJoin(
                'b',
                'nglayouts_block_translation',
                'bt',
                $query->expr()->andX(
                    $query->expr()->eq('bt.block_id', 'b.id'),
                    $query->expr()->eq('bt.status', 'b.status')
                )
            );

        return $query;
    }

    /**
     * Builds and returns a block database SELECT query.
     */
    private function getBlockWithLayoutSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT b.*, bt.*, l.uuid as layout_uuid')
            ->from('nglayouts_block', 'b')
            ->innerJoin(
                'b',
                'nglayouts_block_translation',
                'bt',
                $query->expr()->andX(
                    $query->expr()->eq('bt.block_id', 'b.id'),
                    $query->expr()->eq('bt.status', 'b.status')
                )
            )->innerJoin(
                'b',
                'nglayouts_layout',
                'l',
                $query->expr()->andX(
                    $query->expr()->eq('l.id', 'b.layout_id'),
                    $query->expr()->eq('l.status', 'b.status')
                )
            );

        return $query;
    }
}
