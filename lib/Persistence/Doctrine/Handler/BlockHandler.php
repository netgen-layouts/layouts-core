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
     * Loads all blocks from zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks($layoutId, $zoneIdentifier, $status)
    {
        $data = $this->queryHandler->loadZoneBlocksData($layoutId, $zoneIdentifier, $status);

        if (empty($data)) {
            return array();
        }

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference[]
     */
    public function loadCollectionReferences($blockId, $status)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($blockId, $status);

        if (empty($data)) {
            return array();
        }

        return $this->blockMapper->mapCollectionReferences($data);
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
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, $layoutId, $zoneIdentifier, $status, $position = null)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $layoutId,
                $zoneIdentifier,
                $status
            ),
            $position
        );

        $createdBlockId = $this->queryHandler->createBlock(
            new BlockCreateStruct(
                array(
                    'layoutId' => $layoutId,
                    'zoneIdentifier' => $zoneIdentifier,
                    'status' => $status,
                    'position' => $position,
                    'definitionIdentifier' => $blockCreateStruct->definitionIdentifier,
                    'viewType' => $blockCreateStruct->viewType,
                    'itemViewType' => $blockCreateStruct->itemViewType,
                    'name' => $blockCreateStruct->name !== null ? trim($blockCreateStruct->name) : '',
                    'parameters' => $blockCreateStruct->getParameters(),
                )
            )
        );

        return $this->loadBlock($createdBlockId, $status);
    }

    /**
     * Updates a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function updateBlock($blockId, $status, APIBlockUpdateStruct $blockUpdateStruct)
    {
        $block = $this->loadBlock($blockId, $status);

        $this->queryHandler->updateBlock(
            $blockId,
            $status,
            new BlockUpdateStruct(
                array(
                    'viewType' => $blockUpdateStruct->viewType !== null ? $blockUpdateStruct->viewType : $block->viewType,
                    'itemViewType' => $blockUpdateStruct->itemViewType !== null ? $blockUpdateStruct->itemViewType : $block->itemViewType,
                    'name' => $blockUpdateStruct->name !== null ? trim($blockUpdateStruct->name) : $block->name,
                    'parameters' => $blockUpdateStruct->getParameters() + $block->parameters,
                )
            )
        );

        return $this->loadBlock($blockId, $status);
    }

    /**
     * Copies a block with specified ID to a zone with specified identifier.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $status, $zoneIdentifier)
    {
        $block = $this->loadBlock($blockId, $status);

        $position = $this->positionHelper->getNextPosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $zoneIdentifier,
                $status
            )
        );

        $createdBlockId = $this->queryHandler->createBlock(
            new BlockCreateStruct(
                array(
                    'layoutId' => $block->layoutId,
                    'zoneIdentifier' => $zoneIdentifier,
                    'status' => $block->status,
                    'position' => $position,
                    'definitionIdentifier' => $block->definitionIdentifier,
                    'viewType' => $block->viewType,
                    'itemViewType' => $block->itemViewType,
                    'name' => $block->name,
                    'parameters' => $block->parameters,
                )
            )
        );

        return $this->loadBlock($createdBlockId, $block->status);
    }

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
    public function moveBlock($blockId, $status, $position)
    {
        $block = $this->loadBlock($blockId, $status);

        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status
            ),
            $block->position,
            $position
        );

        $this->queryHandler->moveBlock($blockId, $status, $position);

        return $this->loadBlock($blockId, $status);
    }

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
    public function moveBlockToZone($blockId, $status, $zoneIdentifier, $position)
    {
        $block = $this->loadBlock($blockId, $status);

        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $zoneIdentifier,
                $status
            ),
            $position
        );

        $this->queryHandler->moveBlock($blockId, $status, $position, $zoneIdentifier);

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status
            ),
            $block->position
        );

        return $this->loadBlock($blockId, $status);
    }

    /**
     * Creates a new block status.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $newStatus
     */
    public function createBlockStatus($blockId, $status, $newStatus)
    {
        $block = $this->loadBlock($blockId, $status);

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
            $blockId
        );

        $this->createBlockCollectionsStatus($blockId, $status, $newStatus);
    }

    /**
     * Creates a new status for all collections in specified block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $newStatus
     */
    public function createBlockCollectionsStatus($blockId, $status, $newStatus)
    {
        $collectionsData = $this->queryHandler->loadCollectionReferencesData($blockId, $status);
        foreach ($collectionsData as $collectionsDataRow) {
            if (!$this->collectionHandler->isNamedCollection($collectionsDataRow['collection_id'], $collectionsDataRow['collection_status'])) {
                if ($this->collectionHandler->collectionExists($collectionsDataRow['collection_id'], $newStatus)) {
                    $this->collectionHandler->deleteCollection(
                        $collectionsDataRow['collection_id'],
                        $newStatus
                    );
                }

                $this->collectionHandler->createCollectionStatus(
                    $collectionsDataRow['collection_id'],
                    $status,
                    $newStatus
                );

                $newCollectionStatus = $newStatus;
            } else {
                $newCollectionStatus = $collectionsDataRow['collection_status'];
            }

            if (!$this->queryHandler->collectionExists($blockId, $newStatus, $collectionsDataRow['collection_id'], $newCollectionStatus)) {
                $this->addCollectionToBlock(
                    $blockId,
                    $newStatus,
                    $collectionsDataRow['collection_id'],
                    $newCollectionStatus,
                    $collectionsDataRow['identifier'],
                    $collectionsDataRow['start'],
                    $collectionsDataRow['length']
                );
            }
        }
    }

    /**
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlock($blockId, $status)
    {
        $block = $this->loadBlock($blockId, $status);

        $this->deleteBlockCollections($blockId, $status);
        $this->queryHandler->deleteBlock($blockId, $status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status
            ),
            $block->position
        );
    }

    /**
     * Deletes all block collections.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlockCollections($blockId, $status)
    {
        $collectionReferences = $this->loadCollectionReferences(
            $blockId,
            $status
        );

        foreach ($collectionReferences as $collectionReference) {
            if (!$this->collectionHandler->isNamedCollection($collectionReference->collectionId, $collectionReference->collectionStatus)) {
                $this->collectionHandler->deleteCollection(
                    $collectionReference->collectionId,
                    $collectionReference->collectionStatus
                );
            }

            $this->removeCollectionFromBlock(
                $blockId,
                $status,
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );
        }
    }

    /**
     * Returns if provided collection identifier already exists in the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function collectionIdentifierExists($blockId, $status, $identifier)
    {
        return $this->queryHandler->collectionIdentifierExists($blockId, $status, $identifier);
    }

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
    public function collectionExists($blockId, $status, $collectionId, $collectionStatus)
    {
        return $this->queryHandler->collectionExists($blockId, $status, $collectionId, $collectionStatus);
    }

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
    public function addCollectionToBlock($blockId, $blockStatus, $collectionId, $collectionStatus, $identifier, $offset = 0, $limit = null)
    {
        $this->queryHandler->addCollectionToBlock($blockId, $blockStatus, $collectionId, $collectionStatus, $identifier, $offset, $limit);
    }

    /**
     * Removes the collection from the block.
     *
     * @param int|string $blockId
     * @param int $blockStatus
     * @param int|string $collectionId
     * @param int $collectionStatus
     */
    public function removeCollectionFromBlock($blockId, $blockStatus, $collectionId, $collectionStatus)
    {
        $this->queryHandler->removeCollectionFromBlock($blockId, $blockStatus, $collectionId, $collectionStatus);
    }

    /**
     * Builds the condition array that will be used with position helper.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return array
     */
    protected function getPositionHelperConditions($layoutId, $zoneIdentifier, $status)
    {
        return array(
            'table' => 'ngbm_block',
            'column' => 'position',
            'conditions' => array(
                'layout_id' => $layoutId,
                'zone_identifier' => $zoneIdentifier,
                'status' => $status,
            ),
        );
    }
}
