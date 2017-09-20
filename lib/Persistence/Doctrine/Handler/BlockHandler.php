<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\TranslationUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

class BlockHandler implements BlockHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler
     */
    private $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
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

    public function loadBlock($blockId, $status)
    {
        $data = $this->queryHandler->loadBlockData($blockId, $status);

        if (empty($data)) {
            throw new NotFoundException('block', $blockId);
        }

        $data = $this->blockMapper->mapBlocks($data);

        return reset($data);
    }

    public function blockExists($blockId, $status)
    {
        return $this->queryHandler->blockExists($blockId, $status);
    }

    public function loadLayoutBlocks(Layout $layout)
    {
        $data = $this->queryHandler->loadLayoutBlocksData($layout);

        return $this->blockMapper->mapBlocks($data);
    }

    public function loadZoneBlocks(Zone $zone)
    {
        $data = $this->queryHandler->loadZoneBlocksData($zone);

        return $this->blockMapper->mapBlocks($data);
    }

    public function loadChildBlocks(Block $block, $placeholder = null)
    {
        $data = $this->queryHandler->loadChildBlocksData($block, $placeholder);

        return $this->blockMapper->mapBlocks($data);
    }

    public function loadCollectionReference(Block $block, $identifier)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block, $identifier);

        if (empty($data)) {
            throw new NotFoundException('collection reference', $identifier);
        }

        $data = $this->blockMapper->mapCollectionReferences($data);

        return reset($data);
    }

    public function loadCollectionReferences(Block $block)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block);

        return $this->blockMapper->mapCollectionReferences($data);
    }

    public function createBlock(BlockCreateStruct $blockCreateStruct, Layout $layout, Block $targetBlock = null, $placeholder = null)
    {
        if ($targetBlock !== null && $targetBlock->layoutId !== $layout->id) {
            throw new BadStateException('targetBlock', 'Target block is not in the provided layout.');
        }

        $newBlock = new Block(
            array(
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
                'name' => trim($blockCreateStruct->name),
                'isTranslatable' => $blockCreateStruct->isTranslatable,
                'alwaysAvailable' => $blockCreateStruct->alwaysAvailable,
                'mainLocale' => $layout->mainLocale,
                'status' => $blockCreateStruct->status,
            )
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

    public function createBlockTranslation(Block $block, $locale, $sourceLocale)
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

    public function createCollectionReference(Block $block, CollectionReferenceCreateStruct $createStruct)
    {
        $newCollectionReference = new CollectionReference(
            array(
                'blockId' => $block->id,
                'blockStatus' => $block->status,
                'collectionId' => $createStruct->collection->id,
                'collectionStatus' => $createStruct->collection->status,
                'identifier' => $createStruct->identifier,
                'offset' => $createStruct->offset,
                'limit' => $createStruct->limit,
            )
        );

        $this->queryHandler->createCollectionReference($newCollectionReference);

        return $newCollectionReference;
    }

    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        $updatedBlock = clone $block;

        if ($blockUpdateStruct->viewType !== null) {
            $updatedBlock->viewType = (string) $blockUpdateStruct->viewType;
        }

        if ($blockUpdateStruct->itemViewType !== null) {
            $updatedBlock->itemViewType = (string) $blockUpdateStruct->itemViewType;
        }

        if ($blockUpdateStruct->isTranslatable !== null) {
            $updatedBlock->isTranslatable = (bool) $blockUpdateStruct->isTranslatable;
        }

        if ($blockUpdateStruct->alwaysAvailable !== null) {
            $updatedBlock->alwaysAvailable = (bool) $blockUpdateStruct->alwaysAvailable;
        }

        if ($blockUpdateStruct->name !== null) {
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

            $collectionUpdateStruct = new CollectionUpdateStruct(
                array(
                    'alwaysAvailable' => $updatedBlock->alwaysAvailable,
                    'isTranslatable' => $updatedBlock->isTranslatable,
                )
            );

            $this->collectionHandler->updateCollection($collection, $collectionUpdateStruct);
        }

        return $updatedBlock;
    }

    public function updateBlockTranslation(Block $block, $locale, TranslationUpdateStruct $translationUpdateStruct)
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

    public function setMainTranslation(Block $block, $mainLocale)
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

    public function updateCollectionReference(CollectionReference $collectionReference, CollectionReferenceUpdateStruct $updateStruct)
    {
        $updatedCollectionReference = clone $collectionReference;

        if ($updateStruct->offset !== null) {
            $updatedCollectionReference->offset = (int) $updateStruct->offset;
        }

        if ($updateStruct->limit !== null) {
            $updatedCollectionReference->limit = (int) $updateStruct->limit;
        }

        if ($updateStruct->collection instanceof Collection) {
            $updatedCollectionReference->collectionId = $updateStruct->collection->id;
            $updatedCollectionReference->collectionStatus = $updateStruct->collection->status;
        }

        $this->queryHandler->updateCollectionReference($updatedCollectionReference);

        return $updatedCollectionReference;
    }

    public function copyBlock(Block $block, Block $targetBlock, $placeholder)
    {
        if (strpos($targetBlock->path, $block->path) === 0) {
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

        $newBlock->position = $this->positionHelper->getNextPosition(
            $this->getPositionHelperConditions(
                $targetBlock->id,
                $targetBlock->status,
                $placeholder
            )
        );

        $newBlock = $this->queryHandler->createBlock($newBlock);

        foreach ($block->availableLocales as $locale) {
            $this->queryHandler->createBlockTranslation($newBlock, $locale);
        }

        $this->copyBlockCollections($block, $newBlock);

        foreach ($this->loadChildBlocks($block) as $childBlock) {
            $this->copyBlock($childBlock, $newBlock, $childBlock->placeholder);
        }

        return $newBlock;
    }

    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position)
    {
        if ($block->parentId === $targetBlock->id && $block->placeholder === $placeholder) {
            throw new BadStateException('targetBlock', 'Block is already in specified target block and placeholder.');
        }

        if (strpos($targetBlock->path, $block->path) === 0) {
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

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->parentId,
                $block->status,
                $block->placeholder
            ),
            $block->position
        );

        return $this->loadBlock($block->id, $block->status);
    }

    public function moveBlockToPosition(Block $block, $position)
    {
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

    public function createBlockStatus(Block $block, $newStatus)
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

    public function restoreBlock(Block $block, $fromStatus)
    {
        if ($block->status === $fromStatus) {
            throw new BadStateException('block', 'Block is already in provided status.');
        }

        $fromBlock = $this->loadBlock($block->id, $fromStatus);

        $this->deleteBlocks(array($block->id), $block->status);
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

    public function deleteBlock(Block $block)
    {
        $blockIds = $this->queryHandler->loadSubBlockIds($block->id, $block->status);
        $this->deleteBlocks($blockIds, $block->status);
        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->parentId,
                $block->status,
                $block->placeholder
            ),
            $block->position
        );
    }

    public function deleteBlockTranslation(Block $block, $locale)
    {
        if (!in_array($locale, $block->availableLocales, true)) {
            throw new BadStateException('locale', 'Block does not have the provided locale.');
        }

        if ($locale === $block->mainLocale) {
            throw new BadStateException('locale', 'Main translation cannot be removed from the block.');
        }

        $this->queryHandler->deleteBlockTranslations(array($block->id), $block->status, $locale);

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

    public function deleteLayoutBlocks($layoutId, $status = null)
    {
        $blockIds = $this->queryHandler->loadLayoutBlockIds($layoutId, $status);
        $this->deleteBlocks($blockIds, $status);
    }

    public function deleteBlocks(array $blockIds, $status = null)
    {
        $this->deleteBlockCollections($blockIds, $status);
        $this->queryHandler->deleteBlockTranslations($blockIds, $status);
        $this->queryHandler->deleteBlocks($blockIds, $status);
    }

    /**
     * Copies all block collections to another block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     */
    private function copyBlockCollections(Block $block, Block $targetBlock)
    {
        $collectionReferences = $this->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $collection = $this->collectionHandler->copyCollection($collection);

            $newCollectionReference = new CollectionReference(
                array(
                    'blockId' => $targetBlock->id,
                    'blockStatus' => $targetBlock->status,
                    'collectionId' => $collection->id,
                    'collectionStatus' => $collection->status,
                    'identifier' => $collectionReference->identifier,
                    'offset' => $collectionReference->offset,
                    'limit' => $collectionReference->limit,
                )
            );

            $this->queryHandler->createCollectionReference($newCollectionReference);
        }
    }

    /**
     * Creates a new status for all collections in specified block.
     *
     * This method does not create new status for sub-block collections,
     * so any process that works with this method needs to take care of that.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $newStatus
     */
    private function createBlockCollectionsStatus(Block $block, $newStatus)
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

            $newCollectionReference = new CollectionReference(
                array(
                    'blockId' => $block->id,
                    'blockStatus' => $newStatus,
                    'collectionId' => $collection->id,
                    'collectionStatus' => $collection->status,
                    'identifier' => $collectionReference->identifier,
                    'offset' => $collectionReference->offset,
                    'limit' => $collectionReference->limit,
                )
            );

            $this->queryHandler->createCollectionReference($newCollectionReference);
        }
    }

    /**
     * Deletes block collections with specified block IDs.
     *
     * This method does not delete block collections from sub-blocks,
     * so this should be used only when deleting the entire layout.
     *
     * @param array $blockIds
     * @param int $status
     */
    private function deleteBlockCollections(array $blockIds, $status = null)
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
    private function getPositionHelperConditions($parentId, $status, $placeholder)
    {
        return array(
            'table' => 'ngbm_block',
            'column' => 'position',
            'conditions' => array(
                'parent_id' => $parentId,
                'status' => $status,
                'placeholder' => $placeholder,
            ),
        );
    }
}
