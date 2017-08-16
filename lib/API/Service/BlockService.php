<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

interface BlockService extends Service
{
    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlock($blockId, $locales = null);

    /**
     * Loads a block draft with specified ID.
     *
     * @param int|string $blockId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlockDraft($blockId, $locales = null);

    /**
     * Loads all blocks belonging to provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param string[]|bool $locales
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function loadZoneBlocks(Zone $zone, $locales = null);

    /**
     * Loads all blocks belonging to provided layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string[]|bool $locales
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function loadLayoutBlocks(Layout $layout, $locales = null);

    /**
     * Returns if provided block has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function hasPublishedState(Block $block);

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
     * Creates a block in specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
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
     *                                                          If block does not have a specified translation
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Copies a block to a specified target block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If source or target block is not a draft
     *                                                          If target block is not a container
     *                                                          If target block is in a different layout
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
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If zone is in a different layout
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
     *                                                          If target block is in a different layout
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
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
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
     * Restores the specified block from the published status. Position of the block is kept as is.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function restoreBlock(Block $block);

    /**
     * Enables translating the block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function enableTranslations(Block $block);

    /**
     * Disable translating the block. All translations (except the main one) will be removed.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function disableTranslations(Block $block);

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
     * @param string $locale
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct
     */
    public function newBlockUpdateStruct($locale, Block $block = null);
}
