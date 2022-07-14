<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Layout\Layout;

use function array_column;
use function array_map;
use function count;

final class BlockQueryHandler extends QueryHandler
{
    /**
     * Loads all block data.
     *
     * @param int|string $blockId
     *
     * @return mixed[]
     */
    public function loadBlockData($blockId, int $status): array
    {
        $query = $this->getBlockWithLayoutSelectQuery();

        $this->applyIdCondition($query, $blockId, 'b.id', 'b.uuid');
        $this->applyStatusCondition($query, $status, 'b.status');

        $blocksData = $query->execute()->fetchAllAssociative();

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
     * Loads all layout block data.
     *
     * @return mixed[]
     */
    public function loadLayoutBlocksData(Layout $layout): array
    {
        $query = $this->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('b.layout_id', ':layout_id'),
        )
        ->setParameter('layout_id', $layout->id, Types::INTEGER);

        $this->applyStatusCondition($query, $layout->status, 'b.status');

        $blocksData = $query->execute()->fetchAllAssociative();

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
     * Loads child block data from specified block, optionally filtered by placeholder.
     *
     * This method return only the first level of blocks under the specified block.
     *
     * @return mixed[]
     */
    public function loadChildBlocksData(Block $block, ?string $placeholder = null): array
    {
        $query = $this->getBlockWithLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('b.parent_id', ':parent_id'),
        )
        ->setParameter('parent_id', $block->id, Types::INTEGER)
        ->addOrderBy('b.placeholder', 'ASC')
        ->addOrderBy('b.position', 'ASC');

        if ($placeholder !== null) {
            $query->andWhere(
                $query->expr()->eq('b.placeholder', ':placeholder'),
            )
            ->setParameter('placeholder', $placeholder, Types::STRING);
        }

        $this->applyStatusCondition($query, $block->status, 'b.status');

        $blocksData = $query->execute()->fetchAllAssociative();

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
     */
    public function blockExists($blockId, int $status): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_block');

        $this->applyIdCondition($query, $blockId);
        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAllAssociative();

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
                ],
            )
            ->setValue('id', $block->id ?? $this->connectionHelper->nextId('nglayouts_block'))
            ->setParameter('status', $block->status, Types::INTEGER)
            ->setParameter('uuid', $block->uuid, Types::STRING)
            ->setParameter('layout_id', $block->layoutId, Types::INTEGER)
            ->setParameter('depth', $block->depth, Types::STRING)
            // Materialized path is updated after block is created
            ->setParameter('path', $block->path, Types::STRING)
            ->setParameter('parent_id', $block->parentId, Types::INTEGER)
            ->setParameter('placeholder', $block->placeholder, Types::STRING)
            ->setParameter('position', $block->position, Types::INTEGER)
            ->setParameter('definition_identifier', $block->definitionIdentifier, Types::STRING)
            ->setParameter('view_type', $block->viewType, Types::STRING)
            ->setParameter('item_view_type', $block->itemViewType, Types::STRING)
            ->setParameter('name', $block->name, Types::STRING)
            ->setParameter('translatable', $block->isTranslatable, Types::BOOLEAN)
            ->setParameter('always_available', $block->alwaysAvailable, Types::BOOLEAN)
            ->setParameter('main_locale', $block->mainLocale, Types::STRING)
            ->setParameter('config', $block->config, Types::JSON);

        $query->execute();

        $block->id ??= (int) $this->connectionHelper->lastId('nglayouts_block');

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
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $block->id, Types::INTEGER)
            ->setParameter('path', $block->path, Types::STRING);

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
                ],
            )
            ->setParameter('block_id', $block->id, Types::INTEGER)
            ->setParameter('status', $block->status, Types::INTEGER)
            ->setParameter('locale', $locale, Types::STRING)
            ->setParameter('parameters', $block->parameters[$locale], Types::JSON);

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
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $block->id, Types::INTEGER)
            ->setParameter('uuid', $block->uuid, Types::STRING)
            ->setParameter('layout_id', $block->layoutId, Types::INTEGER)
            ->setParameter('depth', $block->depth, Types::STRING)
            ->setParameter('path', $block->path, Types::STRING)
            ->setParameter('parent_id', $block->parentId, Types::INTEGER)
            ->setParameter('placeholder', $block->placeholder, Types::STRING)
            ->setParameter('position', $block->position, Types::INTEGER)
            ->setParameter('definition_identifier', $block->definitionIdentifier, Types::STRING)
            ->setParameter('view_type', $block->viewType, Types::STRING)
            ->setParameter('item_view_type', $block->itemViewType, Types::STRING)
            ->setParameter('name', $block->name, Types::STRING)
            ->setParameter('translatable', $block->isTranslatable, Types::BOOLEAN)
            ->setParameter('main_locale', $block->mainLocale, Types::STRING)
            ->setParameter('always_available', $block->alwaysAvailable, Types::BOOLEAN)
            ->setParameter('config', $block->config, Types::JSON);

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
                $query->expr()->and(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('locale', ':locale'),
                ),
            )
            ->setParameter('block_id', $block->id, Types::INTEGER)
            ->setParameter('locale', $locale, Types::STRING)
            ->setParameter('parameters', $block->parameters[$locale], Types::JSON);

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
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $block->id, Types::INTEGER)
            ->setParameter('position', $position, Types::INTEGER)
            ->setParameter('parent_id', $targetBlock->id, Types::INTEGER)
            ->setParameter('placeholder', $placeholder, Types::STRING);

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
                $query->expr()->like('path', ':path'),
            )
            ->setParameter('layout_id', $targetBlock->layoutId, Types::INTEGER)
            ->setParameter('depth_difference', $depthDifference, Types::INTEGER)
            ->setParameter('old_path', $block->path, Types::STRING)
            ->setParameter('new_path', $targetBlock->path . $block->id . '/', Types::STRING)
            ->setParameter('path', $block->path . '%', Types::STRING);

        $this->applyStatusCondition($query, $block->status);

        $query->execute();
    }

    /**
     * Deletes all blocks with provided IDs.
     *
     * @param int[] $blockIds
     */
    public function deleteBlocks(array $blockIds, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_block')
            ->where(
                $query->expr()->in('id', [':id']),
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
     * @param int[] $blockIds
     */
    public function deleteBlockTranslations(array $blockIds, ?int $status = null, ?string $locale = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_block_translation')
            ->where(
                $query->expr()->in('block_id', [':block_id']),
            )
            ->setParameter('block_id', $blockIds, Connection::PARAM_INT_ARRAY);

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
     * Loads all sub block IDs.
     *
     * @return int[]
     */
    public function loadSubBlockIds(int $blockId, ?int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_block')
            ->where(
                $query->expr()->like('path', ':path'),
            )
            ->setParameter('path', '%/' . $blockId . '/%', Types::STRING);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->execute()->fetchAllAssociative();

        return array_map('intval', array_column($result, 'id'));
    }

    /**
     * Loads all layout block IDs.
     *
     * @return int[]
     */
    public function loadLayoutBlockIds(int $layoutId, ?int $status = null): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id'),
            )
            ->setParameter('layout_id', $layoutId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $result = $query->execute()->fetchAllAssociative();

        return array_map('intval', array_column($result, 'id'));
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
                $query->expr()->eq('b.id', ':id'),
            )
            ->setParameter('id', $blockId, Types::INTEGER);

        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAllAssociative();

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
                $query->expr()->and(
                    $query->expr()->eq('bt.block_id', 'b.id'),
                    $query->expr()->eq('bt.status', 'b.status'),
                ),
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
                $query->expr()->and(
                    $query->expr()->eq('bt.block_id', 'b.id'),
                    $query->expr()->eq('bt.status', 'b.status'),
                ),
            )->innerJoin(
                'b',
                'nglayouts_layout',
                'l',
                $query->expr()->and(
                    $query->expr()->eq('l.id', 'b.layout_id'),
                    $query->expr()->eq('l.status', 'b.status'),
                ),
            );

        return $query;
    }
}
