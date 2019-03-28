<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockTranslationUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

final class BlockHandler implements BlockHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler
     */
    private $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper
     */
    private $blockMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    private $positionHelper;

    public function __construct(
        BlockQueryHandler $queryHandler,
        CollectionHandlerInterface $collectionHandler,
        BlockMapper $blockMapper,
        PositionHelper $positionHelper
    ) {
        $this->queryHandler = $queryHandler;
        $this->collectionHandler = $collectionHandler;
        $this->blockMapper = $blockMapper;
        $this->positionHelper = $positionHelper;
    }

    public function loadBlock($blockId, int $status): Block
    {
        $data = $this->queryHandler->loadBlockData($blockId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('block', $blockId);
        }

        return $this->blockMapper->mapBlocks($data)[0];
    }

    public function blockExists($blockId, int $status): bool
    {
        return $this->queryHandler->blockExists($blockId, $status);
    }

    public function loadLayoutBlocks(Layout $layout): array
    {
        $data = $this->queryHandler->loadLayoutBlocksData($layout);

        return $this->blockMapper->mapBlocks($data);
    }

    public function loadZoneBlocks(Zone $zone): array
    {
        $data = $this->queryHandler->loadZoneBlocksData($zone);

        return $this->blockMapper->mapBlocks($data);
    }

    public function loadChildBlocks(Block $block, ?string $placeholder = null): array
    {
        $data = $this->queryHandler->loadChildBlocksData($block, $placeholder);

        return $this->blockMapper->mapBlocks($data);
    }

    public function loadCollectionReference(Block $block, string $identifier): CollectionReference
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block, $identifier);

        if (count($data) === 0) {
            throw new NotFoundException('collection reference', $identifier);
        }

        return $this->blockMapper->mapCollectionReferences($data)[0];
    }

    public function loadCollectionReferences(Block $block): array
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block);

        return $this->blockMapper->mapCollectionReferences($data);
    }

    public function createBlock(BlockCreateStruct $blockCreateStruct, Layout $layout, ?Block $targetBlock = null, ?string $placeholder = null): Block
    {
        if ($targetBlock !== null && $targetBlock->layoutId !== $layout->id) {
            throw new BadStateException('targetBlock', 'Target block is not in the provided layout.');
        }

        $newBlock = Block::fromArray(
            [
                'depth' => $targetBlock !== null ? $targetBlock->depth + 1 : 0,
                'path' => $targetBlock !== null ? $targetBlock->path : '/',
                'parentId' => $targetBlock !== null ? $targetBlock->id : null,
                'placeholder' => $targetBlock !== null ? $placeholder : null,
                'layoutId' => $layout->id,
                'position' => $targetBlock !== null ? $blockCreateStruct->position : null,
                'definitionIdentifier' => $blockCreateStruct->definitionIdentifier,
                'config' => $blockCreateStruct->config,
                'viewType' => $blockCreateStruct->viewType,
                'itemViewType' => $blockCreateStruct->itemViewType,
                'name' => trim($blockCreateStruct->name ?? ''),
                'isTranslatable' => $blockCreateStruct->isTranslatable,
                'alwaysAvailable' => $blockCreateStruct->alwaysAvailable,
                'mainLocale' => $layout->mainLocale,
                'status' => $blockCreateStruct->status,
            ]
        );

        if ($targetBlock !== null && $placeholder !== null) {
            $newBlock->position = $this->positionHelper->createPosition(
                $this->getPositionHelperConditions(
                    $targetBlock->id,
                    $targetBlock->status,
                    $placeholder
                ),
                $newBlock->position
            );
        }

        $newBlock = $this->queryHandler->createBlock($newBlock);

        foreach ($layout->availableLocales as $locale) {
            if ($locale === $layout->mainLocale || $newBlock->isTranslatable) {
                $newBlock->availableLocales[] = $locale;
                $newBlock->parameters[$locale] = $blockCreateStruct->parameters;

                $this->queryHandler->createBlockTranslation($newBlock, $locale);
            }
        }

        return $newBlock;
    }

    public function createBlockTranslation(Block $block, string $locale, string $sourceLocale): Block
    {
        if (in_array($locale, $block->availableLocales, true)) {
            throw new BadStateException('locale', 'Block already has the provided locale.');
        }

        if (!in_array($sourceLocale, $block->availableLocales, true)) {
            throw new BadStateException('locale', 'Block does not have the provided source locale.');
        }

        $updatedBlock = clone $block;
        $updatedBlock->availableLocales[] = $locale;
        $updatedBlock->parameters[$locale] = $updatedBlock->parameters[$sourceLocale];

        $this->queryHandler->createBlockTranslation($updatedBlock, $locale);

        $collectionReferences = $this->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $this->collectionHandler->createCollectionTranslation($collection, $locale, $sourceLocale);
        }

        return $updatedBlock;
    }

    public function createCollectionReference(Block $block, Collection $collection, string $identifier): CollectionReference
    {
        $newCollectionReference = CollectionReference::fromArray(
            [
                'blockId' => $block->id,
                'blockStatus' => $block->status,
                'collectionId' => $collection->id,
                'collectionStatus' => $collection->status,
                'identifier' => $identifier,
            ]
        );

        $this->queryHandler->createCollectionReference($newCollectionReference);

        return $newCollectionReference;
    }

    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct): Block
    {
        $updatedBlock = clone $block;

        if (is_string($blockUpdateStruct->viewType)) {
            $updatedBlock->viewType = $blockUpdateStruct->viewType;
        }

        if (is_string($blockUpdateStruct->itemViewType)) {
            $updatedBlock->itemViewType = $blockUpdateStruct->itemViewType;
        }

        if (is_bool($blockUpdateStruct->isTranslatable)) {
            $updatedBlock->isTranslatable = $blockUpdateStruct->isTranslatable;
        }

        if (is_bool($blockUpdateStruct->alwaysAvailable)) {
            $updatedBlock->alwaysAvailable = $blockUpdateStruct->alwaysAvailable;
        }

        if (is_string($blockUpdateStruct->name)) {
            $updatedBlock->name = trim($blockUpdateStruct->name);
        }

        if (is_array($blockUpdateStruct->config)) {
            $updatedBlock->config = $blockUpdateStruct->config;
        }

        $this->queryHandler->updateBlock($updatedBlock);

        $collectionReferences = $this->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $collectionUpdateStruct = CollectionUpdateStruct::fromArray(
                [
                    'alwaysAvailable' => $updatedBlock->alwaysAvailable,
                    'isTranslatable' => $updatedBlock->isTranslatable,
                ]
            );

            $this->collectionHandler->updateCollection($collection, $collectionUpdateStruct);
        }

        return $updatedBlock;
    }

    public function updateBlockTranslation(Block $block, string $locale, BlockTranslationUpdateStruct $translationUpdateStruct): Block
    {
        $updatedBlock = clone $block;

        if (!in_array($locale, $block->availableLocales, true)) {
            throw new BadStateException('locale', 'Block does not have the provided locale.');
        }

        if (is_array($translationUpdateStruct->parameters)) {
            $updatedBlock->parameters[$locale] = $translationUpdateStruct->parameters;
        }

        $this->queryHandler->updateBlockTranslation($updatedBlock, $locale);

        return $updatedBlock;
    }

    public function setMainTranslation(Block $block, string $mainLocale): Block
    {
        if (!in_array($mainLocale, $block->availableLocales, true)) {
            throw new BadStateException('mainLocale', 'Block does not have the provided locale.');
        }

        $updatedBlock = clone $block;
        $updatedBlock->mainLocale = $mainLocale;

        $this->queryHandler->updateBlock($updatedBlock);

        $collectionReferences = $this->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $this->collectionHandler->setMainTranslation($collection, $mainLocale);
        }

        return $updatedBlock;
    }

    public function copyBlock(Block $block, Block $targetBlock, string $placeholder, ?int $position = null): Block
    {
        if (mb_strpos($targetBlock->path, $block->path) === 0) {
            throw new BadStateException('targetBlock', 'Block cannot be copied below itself or its children.');
        }

        $newBlock = clone $block;
        $newBlock->id = null;

        $newBlock->layoutId = $targetBlock->layoutId;
        $newBlock->status = $targetBlock->status;
        $newBlock->depth = $targetBlock->depth + 1;
        // This is only the initial path.
        // Full path is updated after we get the block ID.
        $newBlock->path = $targetBlock->path;
        $newBlock->parentId = $targetBlock->id;
        $newBlock->placeholder = $placeholder;

        $newBlock->position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $targetBlock->id,
                $targetBlock->status,
                $placeholder
            ),
            $position
        );

        $newBlock = $this->queryHandler->createBlock($newBlock);

        foreach ($block->availableLocales as $locale) {
            $this->queryHandler->createBlockTranslation($newBlock, $locale);
        }

        $this->copyBlockCollections($block, $newBlock);

        foreach ($this->loadChildBlocks($block) as $childBlock) {
            if (is_string($childBlock->placeholder)) {
                $this->copyBlock($childBlock, $newBlock, $childBlock->placeholder);
            }
        }

        return $newBlock;
    }

    public function moveBlock(Block $block, Block $targetBlock, string $placeholder, int $position): Block
    {
        if ($block->parentId === $targetBlock->id && $block->placeholder === $placeholder) {
            throw new BadStateException('targetBlock', 'Block is already in specified target block and placeholder.');
        }

        if (mb_strpos($targetBlock->path, $block->path) === 0) {
            throw new BadStateException('targetBlock', 'Block cannot be moved below itself or its children.');
        }

        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $targetBlock->id,
                $targetBlock->status,
                $placeholder
            ),
            $position
        );

        $this->queryHandler->moveBlock($block, $targetBlock, $placeholder, $position);

        if ($block->parentId !== null && $block->placeholder !== null && $block->position !== null) {
            $this->positionHelper->removePosition(
                $this->getPositionHelperConditions(
                    $block->parentId,
                    $block->status,
                    $block->placeholder
                ),
                $block->position
            );
        }

        return $this->loadBlock($block->id, $block->status);
    }

    public function moveBlockToPosition(Block $block, int $position): Block
    {
        if ($block->parentId === null || $block->placeholder === null || $block->position === null) {
            throw new BadStateException('position', 'Root blocks cannot be moved.');
        }

        $movedBlock = clone $block;

        $movedBlock->position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperConditions(
                $block->parentId,
                $block->status,
                $block->placeholder
            ),
            $block->position,
            $position
        );

        $this->queryHandler->updateBlock($movedBlock);

        return $movedBlock;
    }

    public function createBlockStatus(Block $block, int $newStatus): Block
    {
        $newBlock = clone $block;
        $newBlock->status = $newStatus;

        $this->queryHandler->createBlock($newBlock, false);

        foreach ($newBlock->availableLocales as $locale) {
            $this->queryHandler->createBlockTranslation($newBlock, $locale);
        }

        $this->createBlockCollectionsStatus($block, $newStatus);

        return $newBlock;
    }

    public function restoreBlock(Block $block, int $fromStatus): Block
    {
        if ($block->status === $fromStatus) {
            throw new BadStateException('block', 'Block is already in provided status.');
        }

        $fromBlock = $this->loadBlock($block->id, $fromStatus);

        $this->deleteBlocks([$block->id], $block->status);
        $newBlock = $this->createBlockStatus($fromBlock, $block->status);

        // We need to make sure to keep the original placement and position

        $newBlock->placeholder = $block->placeholder;
        $newBlock->layoutId = $block->layoutId;
        $newBlock->position = $block->position;
        $newBlock->parentId = $block->parentId;
        $newBlock->path = $block->path;
        $newBlock->depth = $block->depth;

        $this->queryHandler->updateBlock($newBlock);

        return $newBlock;
    }

    public function deleteBlock(Block $block): void
    {
        $blockIds = $this->queryHandler->loadSubBlockIds($block->id, $block->status);
        $this->deleteBlocks($blockIds, $block->status);

        if ($block->parentId !== null && $block->placeholder !== null && $block->position !== null) {
            $this->positionHelper->removePosition(
                $this->getPositionHelperConditions(
                    $block->parentId,
                    $block->status,
                    $block->placeholder
                ),
                $block->position
            );
        }
    }

    public function deleteBlockTranslation(Block $block, string $locale): Block
    {
        if (!in_array($locale, $block->availableLocales, true)) {
            throw new BadStateException('locale', 'Block does not have the provided locale.');
        }

        if ($locale === $block->mainLocale) {
            throw new BadStateException('locale', 'Main translation cannot be removed from the block.');
        }

        $this->queryHandler->deleteBlockTranslations([$block->id], $block->status, $locale);

        $collectionReferences = $this->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $this->collectionHandler->deleteCollectionTranslation($collection, $locale);
        }

        return $this->loadBlock($block->id, $block->status);
    }

    public function deleteLayoutBlocks($layoutId, ?int $status = null): void
    {
        $blockIds = $this->queryHandler->loadLayoutBlockIds($layoutId, $status);
        $this->deleteBlocks($blockIds, $status);
    }

    public function deleteBlocks(array $blockIds, ?int $status = null): void
    {
        $this->deleteBlockCollections($blockIds, $status);
        $this->queryHandler->deleteBlockTranslations($blockIds, $status);
        $this->queryHandler->deleteBlocks($blockIds, $status);
    }

    /**
     * Copies all block collections to another block.
     */
    private function copyBlockCollections(Block $block, Block $targetBlock): void
    {
        $collectionReferences = $this->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $collection = $this->collectionHandler->copyCollection($collection);

            $newCollectionReference = CollectionReference::fromArray(
                [
                    'blockId' => $targetBlock->id,
                    'blockStatus' => $targetBlock->status,
                    'collectionId' => $collection->id,
                    'collectionStatus' => $collection->status,
                    'identifier' => $collectionReference->identifier,
                ]
            );

            $this->queryHandler->createCollectionReference($newCollectionReference);
        }
    }

    /**
     * Creates a new status for all collections in specified block.
     *
     * This method does not create new status for sub-block collections,
     * so any process that works with this method needs to take care of that.
     */
    private function createBlockCollectionsStatus(Block $block, int $newStatus): void
    {
        $collectionReferences = $this->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $collection = $this->collectionHandler->createCollectionStatus(
                $collection,
                $newStatus
            );

            $newCollectionReference = CollectionReference::fromArray(
                [
                    'blockId' => $block->id,
                    'blockStatus' => $newStatus,
                    'collectionId' => $collection->id,
                    'collectionStatus' => $collection->status,
                    'identifier' => $collectionReference->identifier,
                ]
            );

            $this->queryHandler->createCollectionReference($newCollectionReference);
        }
    }

    /**
     * Deletes block collections with specified block IDs.
     *
     * This method does not delete block collections from sub-blocks,
     * so this should be used only when deleting the entire layout.
     */
    private function deleteBlockCollections(array $blockIds, ?int $status = null): void
    {
        $collectionIds = $this->queryHandler->loadBlockCollectionIds($blockIds, $status);
        foreach ($collectionIds as $collectionId) {
            $this->collectionHandler->deleteCollection($collectionId, $status);
        }

        $this->queryHandler->deleteCollectionReferences($blockIds, $status);
    }

    /**
     * Builds the condition array that will be used with position helper.
     *
     * @param int|string $parentId
     * @param int $status
     * @param string $placeholder
     *
     * @return array
     */
    private function getPositionHelperConditions($parentId, int $status, string $placeholder): array
    {
        return [
            'table' => 'ngbm_block',
            'column' => 'position',
            'conditions' => [
                'parent_id' => $parentId,
                'status' => $status,
                'placeholder' => $placeholder,
            ],
        ];
    }
}
