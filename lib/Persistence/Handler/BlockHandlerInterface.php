<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Handler;

use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\BlockCreateStruct;
use Netgen\Layouts\Persistence\Values\Block\BlockTranslationUpdateStruct;
use Netgen\Layouts\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Persistence\Values\Layout\Layout;

interface BlockHandlerInterface
{
    /**
     * Loads a block with specified ID.
     *
     * Block ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $blockId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If block with specified ID does not exist
     */
    public function loadBlock($blockId, int $status): Block;

    /**
     * Returns if block with specified ID exists.
     *
     * Block ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $blockId
     */
    public function blockExists($blockId, int $status): bool;

    /**
     * Loads all blocks from specified layout.
     *
     * @return \Netgen\Layouts\Persistence\Values\Block\Block[]
     */
    public function loadLayoutBlocks(Layout $layout): array;

    /**
     * Loads all blocks from specified block, optionally filtered by placeholder.
     *
     * @return \Netgen\Layouts\Persistence\Values\Block\Block[]
     */
    public function loadChildBlocks(Block $block, ?string $placeholder = null): array;

    /**
     * Creates a block in specified target block.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided position is out of range
     *                                                     If target block does not belong to layout
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Layout $layout, ?Block $targetBlock = null, ?string $placeholder = null): Block;

    /**
     * Creates a block translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If translation with provided locale already exists
     *                                                     If translation with provided source locale does not exist
     */
    public function createBlockTranslation(Block $block, string $locale, string $sourceLocale): Block;

    /**
     * Updates a block with specified ID.
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct): Block;

    /**
     * Updates a block translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If the block does not have the provided locale
     */
    public function updateBlockTranslation(Block $block, string $locale, BlockTranslationUpdateStruct $translationUpdateStruct): Block;

    /**
     * Updates the main translation of the block.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided locale does not exist in the block
     */
    public function setMainTranslation(Block $block, string $mainLocale): Block;

    /**
     * Copies a block to a specified target block and placeholder.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided position is out of range
     *                                                     If target block is within the provided block
     */
    public function copyBlock(Block $block, Block $targetBlock, string $placeholder, ?int $position = null): Block;

    /**
     * Moves a block to specified position in a specified target block and placeholder.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided position is out of range
     *                                                     If block is already in target block and placeholder
     *                                                     If target block is within the provided block
     */
    public function moveBlock(Block $block, Block $targetBlock, string $placeholder, int $position): Block;

    /**
     * Moves a block to specified position in the current placeholder.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided position is out of range
     */
    public function moveBlockToPosition(Block $block, int $position): Block;

    /**
     * Creates a new block status.
     *
     * This method does not create new status for sub-blocks,
     * so any process that works with this method needs to take care of that.
     */
    public function createBlockStatus(Block $block, int $newStatus): Block;

    /**
     * Restores all block data (except placement and position) from the specified status.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block is already in provided status
     */
    public function restoreBlock(Block $block, int $fromStatus): Block;

    /**
     * Deletes a block with specified ID.
     */
    public function deleteBlock(Block $block): void;

    /**
     * Deletes provided block translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If translation with provided locale does not exist
     *                                                     If translation with provided locale is the main block translation
     */
    public function deleteBlockTranslation(Block $block, string $locale): Block;

    /**
     * Deletes all blocks belonging to specified layout.
     */
    public function deleteLayoutBlocks(int $layoutId, ?int $status = null): void;

    /**
     * Deletes provided blocks.
     *
     * This is an internal method that only deletes the blocks with provided IDs.
     *
     * If you want to delete a block and all of its sub-blocks, use self::deleteBlock method.
     *
     * @param int[] $blockIds
     */
    public function deleteBlocks(array $blockIds, ?int $status = null): void;
}
