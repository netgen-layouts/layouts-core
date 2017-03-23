<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

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
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
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
     * Loads all blocks from specified layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]]
     */
    public function loadLayoutBlocks(Layout $layout)
    {
        $data = $this->queryHandler->loadLayoutBlocksData($layout);

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads all blocks from specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]]
     */
    public function loadZoneBlocks(Zone $zone)
    {
        $data = $this->queryHandler->loadZoneBlocksData($zone);

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads all blocks from specified block, optionally filtered by placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $placeholder
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]]
     */
    public function loadChildBlocks(Block $block, $placeholder = null)
    {
        $data = $this->queryHandler->loadChildBlocksData($block, $placeholder);

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads a collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection reference with specified identifier does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
    public function loadCollectionReference(Block $block, $identifier)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block, $identifier);

        if (empty($data)) {
            throw new NotFoundException('collection reference', $identifier);
        }

        $data = $this->blockMapper->mapCollectionReferences($data);

        return reset($data);
    }

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block)
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block);

        return $this->blockMapper->mapCollectionReferences($data);
    }

    /**
     * Creates a block in specified target block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Block $targetBlock = null, $placeholder = null)
    {
        $blockCreateStruct->name = trim($blockCreateStruct->name);

        if ($targetBlock !== null && $placeholder !== null) {
            $blockCreateStruct->position = $this->positionHelper->createPosition(
                $this->getPositionHelperConditions(
                    $targetBlock->id,
                    $targetBlock->status,
                    $placeholder
                ),
                $blockCreateStruct->position
            );
        }

        $createdBlockId = $this->queryHandler->createBlock(
            $blockCreateStruct,
            $targetBlock,
            $placeholder
        );

        return $this->loadBlock($createdBlockId, $blockCreateStruct->status);
    }

    /**
     * Creates the collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct $createStruct
     */
    public function createCollectionReference(Block $block, CollectionReferenceCreateStruct $createStruct)
    {
        $this->queryHandler->createCollectionReference(
            $block->id,
            $block->status,
            $createStruct
        );
    }

    /**
     * Updates a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        $blockUpdateStruct->viewType = $blockUpdateStruct->viewType ?: $block->viewType;
        $blockUpdateStruct->itemViewType = $blockUpdateStruct->itemViewType ?: $block->itemViewType;

        $blockUpdateStruct->name = $blockUpdateStruct->name !== null ?
            trim($blockUpdateStruct->name) :
            $block->name;

        $blockUpdateStruct->parameters = is_array($blockUpdateStruct->parameters) ?
            $blockUpdateStruct->parameters :
            $block->parameters;

        $blockUpdateStruct->config = is_array($blockUpdateStruct->config) ?
            $blockUpdateStruct->config :
            $block->config;

        $blockUpdateStruct->placeholderParameters = is_array($blockUpdateStruct->placeholderParameters) ?
            $blockUpdateStruct->placeholderParameters :
            $block->placeholderParameters;

        $this->queryHandler->updateBlock($block, $blockUpdateStruct);

        return $this->loadBlock($block->id, $block->status);
    }

    /**
     * Updates a collection reference with specified identifier.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReference $collectionReference
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceUpdateStruct $updateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
    public function updateCollectionReference(CollectionReference $collectionReference, CollectionReferenceUpdateStruct $updateStruct)
    {
        $updateStruct->offset = $updateStruct->offset !== null ?
            $updateStruct->offset :
            $collectionReference->offset;

        $updateStruct->limit = $updateStruct->limit !== null ?
            $updateStruct->limit :
            $collectionReference->limit;

        $this->queryHandler->updateCollectionReference($collectionReference, $updateStruct);

        return $this->loadCollectionReference(
            $this->loadBlock(
                $collectionReference->blockId,
                $collectionReference->blockStatus
            ),
            $collectionReference->identifier
        );
    }

    /**
     * Copies a block to a specified target block and placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is within the provided block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function copyBlock(Block $block, Block $targetBlock, $placeholder)
    {
        if (strpos($targetBlock->path, $block->path) === 0) {
            throw new BadStateException('targetBlock', 'Block cannot be copied below itself or its children.');
        }

        $position = $this->positionHelper->getNextPosition(
            $this->getPositionHelperConditions(
                $targetBlock->id,
                $targetBlock->status,
                $placeholder
            )
        );

        $createdBlockId = $this->queryHandler->createBlock(
            new BlockCreateStruct(
                array(
                    'layoutId' => $targetBlock->layoutId,
                    'status' => $targetBlock->status,
                    'position' => $position,
                    'definitionIdentifier' => $block->definitionIdentifier,
                    'viewType' => $block->viewType,
                    'itemViewType' => $block->itemViewType,
                    'name' => $block->name,
                    'placeholderParameters' => $block->placeholderParameters,
                    'parameters' => $block->parameters,
                    'config' => $block->config,
                )
            ),
            $targetBlock,
            $placeholder
        );

        $copiedBlock = $this->loadBlock($createdBlockId, $targetBlock->status);
        $this->copyBlockCollections($block, $copiedBlock);

        foreach ($this->loadChildBlocks($block) as $childBlock) {
            $this->copyBlock($childBlock, $copiedBlock, $childBlock->placeholder);
        }

        return $copiedBlock;
    }

    /**
     * Copies all block collections to another block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     */
    public function copyBlockCollections(Block $block, Block $targetBlock)
    {
        $collectionReferences = $this->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            if (!$collection->shared) {
                $collection = $this->collectionHandler->copyCollection($collection);
            }

            $this->queryHandler->createCollectionReference(
                $targetBlock->id,
                $targetBlock->status,
                new CollectionReferenceCreateStruct(
                    array(
                        'collection' => $collection,
                        'identifier' => $collectionReference->identifier,
                        'offset' => $collectionReference->offset,
                        'limit' => $collectionReference->limit,
                    )
                )
            );
        }
    }

    /**
     * Moves a block to specified position in a specified target block and placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is already in target block and placeholder
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is within the provided block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position)
    {
        if ($block->parentId === $targetBlock->id && $block->placeholder === $placeholder) {
            throw new BadStateException('targetBlock', 'Block is already in specified target block and placeholder.');
        }

        if (strpos($targetBlock->path, $block->path) === 0) {
            throw new BadStateException('targetBlock', 'Block cannot be moved below itself or its children.');
        }

        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $targetBlock->id,
                $targetBlock->status,
                $placeholder
            ),
            $position
        );

        $this->queryHandler->moveBlock($block, $position, $targetBlock, $placeholder);

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->parentId,
                $block->status,
                $block->placeholder
            ),
            $block->position
        );

        return $this->loadBlock($block->id, $block->status);
    }

    /**
     * Moves a block to specified position in the current placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function moveBlockToPosition(Block $block, $position)
    {
        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperConditions(
                $block->parentId,
                $block->status,
                $block->placeholder
            ),
            $block->position,
            $position
        );

        $this->queryHandler->moveBlock($block, $position);

        return $this->loadBlock($block->id, $block->status);
    }

    /**
     * Creates a new block status.
     *
     * This method does not create new status for sub-blocks,
     * so any process that works with this method needs to take care of that.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $newStatus
     */
    public function createBlockStatus(Block $block, $newStatus)
    {
        $this->queryHandler->createBlockStatus($block, $newStatus);
        $this->createBlockCollectionsStatus($block, $newStatus);
    }

    /**
     * Creates a new status for all non shared collections in specified block.
     *
     * This method does not create new status for sub-block collections,
     * so any process that works with this method needs to take care of that.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $newStatus
     */
    public function createBlockCollectionsStatus(Block $block, $newStatus)
    {
        $collectionReferences = $this->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            if (!$collection->shared) {
                $collection = $this->collectionHandler->createCollectionStatus(
                    $collection,
                    $newStatus
                );
            }

            $this->queryHandler->createCollectionReference(
                $block->id,
                $newStatus,
                new CollectionReferenceCreateStruct(
                    array(
                        'collection' => $collection,
                        'identifier' => $collectionReference->identifier,
                        'offset' => $collectionReference->offset,
                        'limit' => $collectionReference->limit,
                    )
                )
            );
        }
    }

    /**
     * Deletes a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     */
    public function deleteBlock(Block $block)
    {
        $blockIds = $this->queryHandler->loadSubBlockIds($block->id, $block->status);

        $this->deleteBlockCollections($blockIds, $block->status);
        $this->queryHandler->deleteBlocks($blockIds, $block->status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->parentId,
                $block->status,
                $block->placeholder
            ),
            $block->position
        );
    }

    /**
     * Deletes all blocks belonging to specified layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayoutBlocks($layoutId, $status = null)
    {
        $blockIds = $this->queryHandler->loadLayoutBlockIds($layoutId, $status);

        $this->deleteBlockCollections($blockIds, $status);
        $this->queryHandler->deleteBlocks($blockIds, $status);
    }

    /**
     * Deletes block collections with specified block IDs.
     *
     * This method does not delete block collections from sub-blocks,
     * so this should be used only when deleting the entire layout.
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
     * Builds the condition array that will be used with position helper.
     *
     * @param int|string $parentId
     * @param int $status
     * @param string $placeholder
     *
     * @return array
     */
    protected function getPositionHelperConditions($parentId, $status, $placeholder)
    {
        return array(
            'table' => 'ngbm_block',
            'column' => 'position',
            'conditions' => array(
                'parent_id' => $parentId,
                'status' => $status,
                'placeholder' => $placeholder,
            ),
        );
    }
}
