<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\Zone;

interface BlockService
{
    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If block ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function loadBlock($blockId);

    /**
     * Loads blocks belonging to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function loadZoneBlocks(Zone $zone);

    /**
     * Loads blocks belonging to specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function loadLayoutBlocks(Layout $layout);

    /**
     * Creates a block in specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If create struct properties have an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Zone $zone);

    /**
     * Updates a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If update struct properties have an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Copies a specified block. If zone is specified, copied block will be
     * placed in it, otherwise, it will be placed in the same zone where source block is.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If specified zone is in a different layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(Block $block, Zone $zone = null);

    /**
     * Moves a block to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If specified zone is in a different layout
     *                                                                  If target zone is the same as current zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(Block $block, Zone $zone);

    /**
     * Deletes a specified block.
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
