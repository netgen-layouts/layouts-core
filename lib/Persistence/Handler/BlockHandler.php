<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;

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
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function loadBlock($blockId, $status);

    /**
     * Loads all blocks from zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks($layoutId, $zoneIdentifier, $status);

    /**
     * Loads a collection reference.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection reference with specified identifier does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference
     */
    public function loadCollectionReference($blockId, $status, $identifier);

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference[]
     */
    public function loadCollectionReferences($blockId, $status);

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
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, $layoutId, $zoneIdentifier, $status, $position = null);

    /**
     * Updates a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function updateBlock($blockId, $status, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Updates a collection reference with specified identifier.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     * @param int|string $collectionId
     * @param int $collectionStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference
     */
    public function updateCollectionReference($blockId, $status, $identifier, $collectionId, $collectionStatus);

    /**
     * Copies a block with specified ID to a zone with specified identifier.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $status, $zoneIdentifier);

    /**
     * Moves a block to specified position in the zone.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock($blockId, $status, $position);

    /**
     * Moves a block to specified position in a specified zone.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlockToZone($blockId, $status, $zoneIdentifier, $position);

    /**
     * Creates a new block status.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $newStatus
     */
    public function createBlockStatus($blockId, $status, $newStatus);

    /**
     * Creates a new status for all collections in specified block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $newStatus
     */
    public function createBlockCollectionsStatus($blockId, $status, $newStatus);

    /**
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlock($blockId, $status);

    /**
     * Deletes all block collections.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlockCollections($blockId, $status);

    /**
     * Returns if provided collection identifier already exists in the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function collectionIdentifierExists($blockId, $status, $identifier);

    /**
     * Returns if provided collection already exists in the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int|string $collectionId
     * @param int $collectionStatus
     *
     * @return bool
     */
    public function collectionExists($blockId, $status, $collectionId, $collectionStatus);

    /**
     * Adds the collection to the block.
     *
     * @param int|string $blockId
     * @param int $blockStatus
     * @param int|string $collectionId
     * @param int $collectionStatus
     * @param string $identifier
     * @param int $offset
     * @param int $limit
     */
    public function addCollectionToBlock($blockId, $blockStatus, $collectionId, $collectionStatus, $identifier, $offset = 0, $limit = null);

    /**
     * Removes the collection from the block.
     *
     * @param int|string $blockId
     * @param int $blockStatus
     * @param int|string $collectionId
     * @param int $collectionStatus
     */
    public function removeCollectionFromBlock($blockId, $blockStatus, $collectionId, $collectionStatus);
}
