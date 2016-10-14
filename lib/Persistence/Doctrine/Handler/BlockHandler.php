<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;

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
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
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
     * Loads all blocks from zone with specified identifier.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks(Zone $zone)
    {
        $data = $this->queryHandler->loadZoneBlocksData($zone->layoutId, $zone->identifier, $zone->status);

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads a collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection reference with specified identifier does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference
     */
    public function loadCollectionReference(Block $block, $identifier)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block->id, $block->status, $identifier);

        if (empty($data)) {
            throw new NotFoundException('collection', $identifier);
        }

        $data = $this->blockMapper->mapCollectionReferences($data);

        return reset($data);
    }

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block->id, $block->status);

        return $this->blockMapper->mapCollectionReferences($data);
    }

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, Layout $layout, $zoneIdentifier, $position = null)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $layout->id,
                $layout->status,
                $zoneIdentifier
            ),
            $position
        );

        $createdBlockId = $this->queryHandler->createBlock(
            new BlockCreateStruct(
                array(
                    'layoutId' => $layout->id,
                    'zoneIdentifier' => $zoneIdentifier,
                    'status' => $layout->status,
                    'position' => $position,
                    'definitionIdentifier' => $blockCreateStruct->definitionIdentifier,
                    'viewType' => $blockCreateStruct->viewType,
                    'itemViewType' => $blockCreateStruct->itemViewType,
                    'name' => $blockCreateStruct->name !== null ? trim($blockCreateStruct->name) : '',
                    'parameters' => $blockCreateStruct->getParameters(),
                )
            )
        );

        return $this->loadBlock($createdBlockId, $layout->status);
    }

    /**
     * Creates the collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string $identifier
     * @param int $offset
     * @param int $limit
     */
    public function createCollectionReference(Block $block, Collection $collection, $identifier, $offset = 0, $limit = null)
    {
        $this->queryHandler->createCollectionReference(
            $block->id,
            $block->status,
            $collection->id,
            $collection->status,
            $identifier,
            $offset,
            $limit
        );
    }

    /**
     * Updates a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function updateBlock(Block $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        $this->queryHandler->updateBlock(
            $block->id,
            $block->status,
            new BlockUpdateStruct(
                array(
                    'viewType' => $blockUpdateStruct->viewType !== null ? $blockUpdateStruct->viewType : $block->viewType,
                    'itemViewType' => $blockUpdateStruct->itemViewType !== null ? $blockUpdateStruct->itemViewType : $block->itemViewType,
                    'name' => $blockUpdateStruct->name !== null ? trim($blockUpdateStruct->name) : $block->name,
                    'parameters' => $blockUpdateStruct->getParameters() + $block->parameters,
                )
            )
        );

        return $this->loadBlock($block->id, $block->status);
    }

    /**
     * Updates a collection reference with specified identifier.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param string $identifier
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference
     */
    public function updateCollectionReference(Block $block, $identifier, Collection $collection)
    {
        $this->queryHandler->updateCollectionReference(
            $block->id,
            $block->status,
            $identifier,
            $collection->id,
            $collection->status
        );

        return $this->loadCollectionReference($block, $identifier);
    }

    /**
     * Copies a block to a specified layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock(Block $block, Layout $layout, $zoneIdentifier)
    {
        $position = $this->positionHelper->getNextPosition(
            $this->getPositionHelperConditions(
                $layout->id,
                $layout->status,
                $zoneIdentifier
            )
        );

        $createdBlockId = $this->queryHandler->createBlock(
            new BlockCreateStruct(
                array(
                    'layoutId' => $layout->id,
                    'zoneIdentifier' => $zoneIdentifier,
                    'status' => $layout->status,
                    'position' => $position,
                    'definitionIdentifier' => $block->definitionIdentifier,
                    'viewType' => $block->viewType,
                    'itemViewType' => $block->itemViewType,
                    'name' => $block->name,
                    'parameters' => $block->parameters,
                )
            )
        );

        $copiedBlock = $this->loadBlock($createdBlockId, $layout->status);

        $this->copyBlockCollections($block, $copiedBlock);

        return $copiedBlock;
    }

    /**
     * Moves a block to specified position in the zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock(Block $block, $position)
    {
        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->status,
                $block->zoneIdentifier
            ),
            $block->position,
            $position
        );

        $this->queryHandler->moveBlock($block->id, $block->status, $position);

        return $this->loadBlock($block->id, $block->status);
    }

    /**
     * Moves a block to specified position in a specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlockToZone(Block $block, $zoneIdentifier, $position)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->status,
                $zoneIdentifier
            ),
            $position
        );

        $this->queryHandler->moveBlock($block->id, $block->status, $position, $zoneIdentifier);

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->status,
                $block->zoneIdentifier
            ),
            $block->position
        );

        return $this->loadBlock($block->id, $block->status);
    }

    /**
     * Creates a new block status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param int $newStatus
     */
    public function createBlockStatus(Block $block, $newStatus)
    {
        $this->queryHandler->createBlock(
            new BlockCreateStruct(
                array(
                    'layoutId' => $block->layoutId,
                    'zoneIdentifier' => $block->zoneIdentifier,
                    'status' => $newStatus,
                    'position' => $block->position,
                    'definitionIdentifier' => $block->definitionIdentifier,
                    'viewType' => $block->viewType,
                    'itemViewType' => $block->itemViewType,
                    'name' => $block->name,
                    'parameters' => $block->parameters,
                )
            ),
            $block->id
        );

        $this->createBlockCollectionsStatus($block, $newStatus);
    }

    /**
     * Deletes a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     */
    public function deleteBlock(Block $block)
    {
        $this->deleteBlocks(array($block->id), $block->status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->status,
                $block->zoneIdentifier
            ),
            $block->position
        );
    }

    /**
     * Deletes blocks with specified IDs.
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
     * Deletes block collections with specified block IDs.
     *
     * @param array $blockIds
     * @param int $status
     */
    public function deleteBlockCollections(array $blockIds, $status = null)
    {
        $collectionIds = $this->queryHandler->loadBlockCollectionIds($blockIds, $status);
        foreach ($collectionIds as $collectionId) {
            $this->collectionHandler->deleteCollection($collectionId, $status);
        }

        $this->queryHandler->deleteCollectionReferences($blockIds, $status);
    }

    /**
     * Copies all block collections to another block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $targetBlock
     */
    protected function copyBlockCollections(Block $block, Block $targetBlock)
    {
        $collectionReferences = $this->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            if (!$this->collectionHandler->isSharedCollection($collection->id)) {
                $collection = $this->collectionHandler->copyCollection($collection);
            }

            $this->queryHandler->createCollectionReference(
                $targetBlock->id,
                $targetBlock->status,
                $collection->id,
                $collection->status,
                $collectionReference->identifier,
                $collectionReference->offset,
                $collectionReference->limit
            );
        }
    }

    /**
     * Creates a new status for all collections in specified block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
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

            if (!$this->collectionHandler->isSharedCollection($collection->id)) {
                $collection = $this->collectionHandler->createCollectionStatus(
                    $collection,
                    $newStatus
                );
            }

            $this->queryHandler->createCollectionReference(
                $block->id,
                $newStatus,
                $collection->id,
                $collection->status,
                $collectionReference->identifier,
                $collectionReference->offset,
                $collectionReference->limit
            );
        }
    }

    /**
     * Builds the condition array that will be used with position helper.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $zoneIdentifier
     *
     * @return array
     */
    protected function getPositionHelperConditions($layoutId, $status, $zoneIdentifier)
    {
        return array(
            'table' => 'ngbm_block',
            'column' => 'position',
            'conditions' => array(
                'layout_id' => $layoutId,
                'status' => $status,
                'zone_identifier' => $zoneIdentifier,
            ),
        );
    }
}
