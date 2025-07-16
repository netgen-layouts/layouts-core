<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Service;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * @method BlockList loadPlaceholderBlocks(Block $block, string $placeholder, ?string[] $locales = null, bool $useMainLocale = true)
 */
interface BlockService extends TransactionService
{
    /**
     * Loads a block with specified ID.
     *
     * By default, block is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the block is always available and $useMainLocale is
     * set to true, block in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If block with specified UUID does not exist
     */
    public function loadBlock(UuidInterface $blockId, ?array $locales = null, bool $useMainLocale = true): Block;

    /**
     * Loads a block draft with specified ID.
     *
     * By default, block is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the block is always available and $useMainLocale is
     * set to true, block in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If block with specified UUID does not exist
     */
    public function loadBlockDraft(UuidInterface $blockId, ?array $locales = null, bool $useMainLocale = true): Block;

    /**
     * Loads all blocks belonging to provided zone. This method DOES NOT load
     * child blocks.
     *
     * By default, block is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the block is always available and $useMainLocale is
     * set to true, block in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     */
    public function loadZoneBlocks(Zone $zone, ?array $locales = null, bool $useMainLocale = true): BlockList;

    /**
     * Loads all blocks belonging to provided placeholder.
     *
     * By default, block is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the block is always available and $useMainLocale is
     * set to true, block in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * Will be added to interface in 2.0.
     *
     * @param string[]|null $locales
     */
    // public function loadPlaceholderBlocks(Block $block, string $placeholder, ?array $locales = null, bool $useMainLocale = true): BlockList;

    /**
     * Loads all blocks belonging to provided layout.
     *
     * By default, block is loaded in main locale.
     *
     * If $locales is an array of strings, the first available locale will
     * be returned. If the block is always available and $useMainLocale is
     * set to true, block in main locale will be returned if none of the
     * locales in $locales array are found.
     *
     * @param string[]|null $locales
     */
    public function loadLayoutBlocks(Layout $layout, ?array $locales = null, bool $useMainLocale = true): BlockList;

    /**
     * Returns if provided block has a published status.
     */
    public function hasPublishedState(Block $block): bool;

    /**
     * Creates a block in specified block and placeholder and at specified position.
     *
     * If position is not provided, bock is placed at the end of the placeholder.
     * If the target block is not translatable, created block will not be translatable,
     * ignoring the translatable flag from the create struct.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If target block is not a draft
     *                                                     If target block is not a container
     *                                                     If placeholder does not exist in the target block
     *                                                     If new block is a container
     *                                                     If provided position is out of range
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Block $targetBlock, string $placeholder, ?int $position = null): Block;

    /**
     * Creates a block in specified zone and at specified position.
     *
     * If position is not provided, block is placed at the end of the zone.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If zone is not a draft
     *                                                     If provided position is out of range
     *                                                     If block cannot be placed in specified zone
     */
    public function createBlockInZone(BlockCreateStruct $blockCreateStruct, Zone $zone, ?int $position = null): Block;

    /**
     * Updates a specified block.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block is not a draft
     *                                                     If block does not have a specified translation
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct): Block;

    /**
     * Copies a block to a specified target block and placeholder.
     *
     * If position is specified, block is copied there, otherwise,
     * the block is placed at the end of placeholder.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If source or target block is not a draft
     *                                                     If target block is not a container
     *                                                     If target block is in a different layout
     *                                                     If placeholder does not exist in the target block
     *                                                     If new block is a container
     *                                                     If target block is within the provided block
     */
    public function copyBlock(Block $block, Block $targetBlock, string $placeholder, ?int $position = null): Block;

    /**
     * Copies a block to a specified zone.
     *
     * If position is specified, block is copied there, otherwise,
     * block is placed at the end of the zone.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block or zone are not drafts
     *                                                     If zone is in a different layout
     *                                                     If block cannot be placed in specified zone as specified by the list of blocks allowed within the zone
     *                                                     If provided position is out of range
     */
    public function copyBlockToZone(Block $block, Zone $zone, ?int $position = null): Block;

    /**
     * Moves a block to specified target block.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If source or target block is not a draft
     *                                                     If target block is not a container
     *                                                     If target block is in a different layout
     *                                                     If placeholder does not exist in the target block
     *                                                     If new block is a container
     *                                                     If target block is within the provided block
     *                                                     If provided position is out of range
     */
    public function moveBlock(Block $block, Block $targetBlock, string $placeholder, int $position): Block;

    /**
     * Moves a block to specified position inside the zone.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block or zone are not drafts
     *                                                     If zone is in a different layout
     *                                                     If provided position is out of range
     *                                                     If block cannot be placed in specified zone as specified by the list of blocks allowed within the zone
     */
    public function moveBlockToZone(Block $block, Zone $zone, int $position): Block;

    /**
     * Restores the specified block from the published status.
     *
     * Placement and position of the block are kept as is.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block is not a draft
     */
    public function restoreBlock(Block $block): Block;

    /**
     * Enables translating the block.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block is not a draft
     * @throws \Netgen\Layouts\Exception\BadStateException If parent block is not translatable
     */
    public function enableTranslations(Block $block): Block;

    /**
     * Disable translating the block. All translations (except the main one) will be removed.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block is not a draft
     */
    public function disableTranslations(Block $block): Block;

    /**
     * Deletes a specified block.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block is not a draft
     */
    public function deleteBlock(Block $block): void;

    /**
     * Creates a new block create struct from data found in provided block definition.
     */
    public function newBlockCreateStruct(BlockDefinitionInterface $blockDefinition): BlockCreateStruct;

    /**
     * Creates a new block update struct in specified locale.
     *
     * If block is provided, initial data is copied from the block.
     */
    public function newBlockUpdateStruct(string $locale, ?Block $block = null): BlockUpdateStruct;
}
