<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

interface BlockService extends Service
{
    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlock($blockId);

    /**
     * Loads a block draft with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlockDraft($blockId);

    /**
     * Loads all blocks belonging to provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]]
     */
    public function loadZoneBlocks(Zone $zone);

    /**
     * Returns if provided block has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function hasPublishedState(Block $block);

    /**
     * Loads the collection reference with specified identifier.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference
     */
    public function loadCollectionReference(Block $block, $identifier);

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block);

    /**
     * Creates a block in specified block and placeholder.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is not a draft
     *                                                          If target block is not a container
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Block $targetBlock, $placeholder, $position = null);

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function createBlockInZone(BlockCreateStruct $blockCreateStruct, Zone $zone, $position = null);

    /**
     * Updates a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Updates a specified collection reference.
     *
     * @param \Netgen\BlockManager\API\Values\Block\CollectionReference $collectionReference
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference
     */
    public function updateCollectionReference(CollectionReference $collectionReference, Collection $collection);

    /**
     * Copies a block to a specified target block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If source or target block is not a draft
     *                                                          If target block is not a container
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If target block is within the provided block
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function copyBlock(Block $block, Block $targetBlock, $placeholder);

    /**
     * Copies a block to a specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function copyBlockToZone(Block $block, Zone $zone);

    /**
     * Moves a block to specified target block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If source or target block is not a draft
     *                                                          If target block is not a container
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If target block is within the provided block
     *                                                          If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position);

    /**
     * Moves a block to specified position inside the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If zone is in a different layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function moveBlockToZone(Block $block, Zone $zone, $position);

    /**
     * Restores the specified block from the published status. Zone and position are kept as is.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If block does not have a published status
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function restoreBlock(Block $block);

    /**
     * Deletes a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     */
    public function deleteBlock(Block $block);

    /**
     * Creates a new block create struct.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    public function newBlockCreateStruct(BlockDefinitionInterface $blockDefinition);

    /**
     * Creates a new block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct
     */
    public function newBlockUpdateStruct(Block $block = null);
}
