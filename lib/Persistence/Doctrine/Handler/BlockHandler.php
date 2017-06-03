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
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

class BlockHandler implements BlockHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler
     */
    protected $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    protected $positionHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler $queryHandler
     * @param \Netgen\BlockManager\Persistence\Handler\CollectionHandler $collectionHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper $positionHelper
     */
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

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function loadBlock($blockId, $status)
    {
        $data = $this->queryHandler->loadBlockData($blockId, $status);

        if (empty($data)) {
            throw new NotFoundException('block', $blockId);
        }

        $data = $this->blockMapper->mapBlocks($data);

        return reset($data);
    }

    /**
     * Returns if block with specified ID exists.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return bool
     */
    public function blockExists($blockId, $status)
    {
        return $this->queryHandler->blockExists($blockId, $status);
    }

    /**
     * Loads all blocks from specified layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadLayoutBlocks(Layout $layout)
    {
        $data = $this->queryHandler->loadLayoutBlocksData($layout);

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads all blocks from specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadZoneBlocks(Zone $zone)
    {
        $data = $this->queryHandler->loadZoneBlocksData($zone);

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads all blocks from specified block, optionally filtered by placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $placeholder
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadChildBlocks(Block $block, $placeholder = null)
    {
        $data = $this->queryHandler->loadChildBlocksData($block, $placeholder);

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads a collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection reference with specified identifier does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
    public function loadCollectionReference(Block $block, $identifier)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block, $identifier);

        if (empty($data)) {
            throw new NotFoundException('collection reference', $identifier);
        }

        $data = $this->blockMapper->mapCollectionReferences($data);

        return reset($data);
    }

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block);

        return $this->blockMapper->mapCollectionReferences($data);
    }

    /**
     * Creates a block in specified target block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Block $targetBlock = null, $placeholder = null)
    {
        $newBlock = new Block(
            array(
                'depth' => $targetBlock !== null ? $targetBlock->depth + 1 : 0,
                'path' => $targetBlock !== null ? $targetBlock->path : '/',
                'parentId' => $targetBlock !== null ? $targetBlock->id : null,
                'placeholder' => $placeholder,
                'layoutId' => $blockCreateStruct->layoutId,
                'position' => $blockCreateStruct->position,
                'definitionIdentifier' => $blockCreateStruct->definitionIdentifier,
                'parameters' => $blockCreateStruct->parameters,
                'config' => $blockCreateStruct->config,
                'viewType' => $blockCreateStruct->viewType,
                'itemViewType' => $blockCreateStruct->itemViewType,
                'name' => trim($blockCreateStruct->name),
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

        return $this->queryHandler->createBlock($newBlock);
    }

    /**
     * Creates the collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct $createStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
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

    /**
     * Updates a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        $updatedBlock = clone $block;

        if ($blockUpdateStruct->viewType !== null) {
            $updatedBlock->viewType = (string) $blockUpdateStruct->viewType;
        }

        if ($blockUpdateStruct->itemViewType !== null) {
            $updatedBlock->itemViewType = (string) $blockUpdateStruct->itemViewType;
        }

        if ($blockUpdateStruct->name !== null) {
            $updatedBlock->name = trim($blockUpdateStruct->name);
        }

        if (is_array($blockUpdateStruct->parameters)) {
            $updatedBlock->parameters = $blockUpdateStruct->parameters;
        }

        if (is_array($blockUpdateStruct->config)) {
            $updatedBlock->config = $blockUpdateStruct->config;
        }

        $this->queryHandler->updateBlock($updatedBlock);

        return $updatedBlock;
    }

    /**
     * Updates a collection reference with specified identifier.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReference $collectionReference
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceUpdateStruct $updateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
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

    /**
     * Copies a block to a specified target block and placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is within the provided block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
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

        $this->copyBlockCollections($block, $newBlock);

        foreach ($this->loadChildBlocks($block) as $childBlock) {
            $this->copyBlock($childBlock, $newBlock, $childBlock->placeholder);
        }

        return $newBlock;
    }

    /**
     * Moves a block to specified position in a specified target block and placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is already in target block and placeholder
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is within the provided block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
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

    /**
     * Moves a block to specified position in the current placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
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

    /**
     * Creates a new block status.
     *
     * This method does not create new status for sub-blocks,
     * so any process that works with this method needs to take care of that.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function createBlockStatus(Block $block, $newStatus)
    {
        $newBlock = clone $block;
        $newBlock->status = $newStatus;

        $this->queryHandler->createBlock($newBlock, false);
        $this->createBlockCollectionsStatus($block, $newStatus);

        return $newBlock;
    }

    /**
     * Restores all block data (except placement and position) from the specified status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $fromStatus
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException if block is already in provided status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
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

    /**
     * Deletes a block with specified ID and all of its sub-blocks.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     */
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

    /**
     * Deletes all blocks belonging to specified layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayoutBlocks($layoutId, $status = null)
    {
        $blockIds = $this->queryHandler->loadLayoutBlockIds($layoutId, $status);
        $this->deleteBlocks($blockIds, $status);
    }

    /**
     * Deletes provided blocks.
     *
     * This is an internal method that only deletes the blocks with provided IDs.
     *
     * If you want to delete a block and all of its sub-blocks, use self::deleteBlock method.
     *
     * @param array $blockIds
     * @param int $status
     */
    public function deleteBlocks(array $blockIds, $status = null)
    {
        $this->deleteBlockCollections($blockIds, $status);
        $this->queryHandler->deleteBlocks($blockIds, $status);
    }

    /**
     * Copies all block collections to another block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     */
    protected function copyBlockCollections(Block $block, Block $targetBlock)
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
    protected function createBlockCollectionsStatus(Block $block, $newStatus)
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
    protected function deleteBlockCollections(array $blockIds, $status = null)
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
    protected function getPositionHelperConditions($parentId, $status, $placeholder)
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
