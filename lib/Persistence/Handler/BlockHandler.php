<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

interface BlockHandler
{
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
    public function loadBlock($blockId, $status);

    /**
     * Returns if block with specified ID exists.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return bool
     */
    public function blockExists($blockId, $status);

    /**
     * Loads all blocks from specified layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadLayoutBlocks(Layout $layout);

    /**
     * Loads all blocks from specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadZoneBlocks(Zone $zone);

    /**
     * Loads all blocks from specified block, optionally filtered by placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $placeholder
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadChildBlocks(Block $block, $placeholder = null);

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
    public function loadCollectionReference(Block $block, $identifier);

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block);

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
    public function createBlock(BlockCreateStruct $blockCreateStruct, Block $targetBlock = null, $placeholder = null);

    /**
     * Creates the collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct $createStruct
     */
    public function createCollectionReference(Block $block, CollectionReferenceCreateStruct $createStruct);

    /**
     * Updates a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Updates a collection reference with specified identifier.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReference $collectionReference
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceUpdateStruct $updateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
    public function updateCollectionReference(CollectionReference $collectionReference, CollectionReferenceUpdateStruct $updateStruct);

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
    public function copyBlock(Block $block, Block $targetBlock, $placeholder);

    /**
     * Copies all block collections to another block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     */
    public function copyBlockCollections(Block $block, Block $targetBlock);

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
    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position);

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
    public function moveBlockToPosition(Block $block, $position);

    /**
     * Creates a new block status.
     *
     * This method does not create new status for sub-blocks,
     * so any process that works with this method needs to take care of that.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $newStatus
     */
    public function createBlockStatus(Block $block, $newStatus);

    /**
     * Creates a new status for all collections in specified block.
     *
     * This method does not create new status for sub-block collections,
     * so any process that works with this method needs to take care of that.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $newStatus
     */
    public function createBlockCollectionsStatus(Block $block, $newStatus);

    /**
     * Deletes a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     */
    public function deleteBlock(Block $block);

    /**
     * Deletes all blocks belonging to specified layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayoutBlocks($layoutId, $status = null);

    /**
     * Deletes block collections with specified block IDs.
     *
     * This method does not delete block collections from sub-blocks,
     * so this should be used only when deleting the entire layout.
     *
     * @param array $blockIds
     * @param int $status
     */
    public function deleteBlockCollections(array $blockIds, $status = null);
}
