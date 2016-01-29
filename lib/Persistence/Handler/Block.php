<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;

interface Block
{
    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function loadBlock($blockId, $status = Layout::STATUS_PUBLISHED);

    /**
     * Loads all blocks from zone with specified ID.
     *
     * @param int|string $zoneId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks($zoneId, $status = Layout::STATUS_PUBLISHED);

    /**
     * Creates a block in specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param int|string $zoneId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, $zoneId);

    /**
     * Updates a block with specified ID.
     *
     * @param int|string $blockId
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function updateBlock($blockId, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Copies a block with specified ID to a zone with specified ID.
     *
     * @param int|string $blockId
     * @param int|string $zoneId
     * @param bool $createNew
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $zoneId = null, $createNew = true, $status = Layout::STATUS_PUBLISHED, $newStatus = Layout::STATUS_DRAFT);

    /**
     * Moves a block to zone with specified ID.
     *
     * @param int|string $blockId
     * @param int|string $zoneId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock($blockId, $zoneId);

    /**
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     */
    public function deleteBlock($blockId);

    /**
     * Deletes all blocks within the specified zone.
     *
     * @param int|string $zoneId
     * @param int $status
     */
    public function deleteZoneBlocks($zoneId, $status = null);
}
