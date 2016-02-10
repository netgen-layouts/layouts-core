<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout;

interface BlockService
{
    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If block ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function loadBlock($blockId, $status = Layout::STATUS_PUBLISHED);

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If provided zone identifier has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout is not in draft status
     *                                                              If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Layout $layout, $zoneIdentifier);

    /**
     * Updates a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Copies a specified block. If zone is specified, copied block will be
     * placed in it, otherwise, it will be placed in the same zone where source block is.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If provided zone identifier has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *                                                              If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(Block $block, $zoneIdentifier = null);

    /**
     * Moves a block to specified position inside the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param int $position
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If provided position or zone identifier have an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *                                                              If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(Block $block, $position, $zoneIdentifier = null);

    /**
     * Deletes a specified block.
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function deleteBlock(Block $block);

    /**
     * Creates a new block create struct.
     *
     * @param string $definitionIdentifier
     * @param string $viewType
     *
     * @return \Netgen\BlockManager\API\Values\BlockCreateStruct
     */
    public function newBlockCreateStruct($definitionIdentifier, $viewType);

    /**
     * Creates a new block update struct.
     *
     * @return \Netgen\BlockManager\API\Values\BlockUpdateStruct
     */
    public function newBlockUpdateStruct();
}
