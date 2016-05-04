<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Exception\BadStateException;
use Exception;

class BlockService implements BlockServiceInterface
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\BlockValidator
     */
    protected $blockValidator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    protected $layoutHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $blockValidator
     * @param \Netgen\BlockManager\Core\Service\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(
        BlockValidator $blockValidator,
        BlockMapper $blockMapper,
        Handler $persistenceHandler
    ) {
        $this->blockValidator = $blockValidator;
        $this->blockMapper = $blockMapper;
        $this->persistenceHandler = $persistenceHandler;

        $this->blockHandler = $persistenceHandler->getBlockHandler();
        $this->layoutHandler = $persistenceHandler->getLayoutHandler();
    }

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function loadBlock($blockId, $status = Layout::STATUS_PUBLISHED)
    {
        $this->blockValidator->validateId($blockId, 'blockId');

        return $this->blockMapper->mapBlock(
            $this->blockHandler->loadBlock(
                $blockId,
                $status
            )
        );
    }

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout is not in draft status
     *                                                              If zone does not exist in the layout
     *                                                              If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, Layout $layout, $zoneIdentifier, $position = null)
    {
        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($position !== null) {
            $this->blockValidator->validatePosition($position, 'position');
        }

        if (!$this->layoutHandler->zoneExists($layout->getId(), $zoneIdentifier, $layout->getStatus())) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
        }

        if ($layout->getStatus() !== Layout::STATUS_DRAFT) {
            throw new BadStateException('layout', 'Blocks can only be created in draft layouts.');
        }

        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdBlock = $this->blockHandler->createBlock(
                $blockCreateStruct,
                $layout->getId(),
                $zoneIdentifier,
                $layout->getStatus(),
                $position
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->blockMapper->mapBlock($createdBlock);
    }

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
    public function updateBlock(Block $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        if ($block->getStatus() !== Layout::STATUS_DRAFT && $block->getStatus() !== Layout::STATUS_TEMPORARY_DRAFT) {
            throw new BadStateException('block', 'Only blocks in (temporary) draft status can be updated.');
        }

        $this->blockValidator->validateBlockUpdateStruct($block, $blockUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedBlock = $this->blockHandler->updateBlock(
                $block->getId(),
                $block->getStatus(),
                $blockUpdateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->blockMapper->mapBlock($updatedBlock);
    }

    /**
     * Copies a specified block. If zone is specified, copied block will be
     * placed in it, otherwise, it will be placed in the same zone where source block is.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *                                                              If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(Block $block, $zoneIdentifier = null)
    {
        if ($block->getStatus() !== Layout::STATUS_DRAFT) {
            throw new BadStateException('block', 'Only blocks in draft status can be copied.');
        }

        if ($zoneIdentifier !== null) {
            $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

            if (!$this->layoutHandler->zoneExists($block->getLayoutId(), $zoneIdentifier, $block->getStatus())) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedBlock = $this->blockHandler->copyBlock(
                $block->getId(),
                $block->getStatus(),
                $zoneIdentifier !== null ? $zoneIdentifier : $block->getZoneIdentifier()
            );

        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->blockMapper->mapBlock($copiedBlock);
    }

    /**
     * Moves a block to specified position inside the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param int $position
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *                                                              If zone does not exist in the layout
     *                                                              If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(Block $block, $position, $zoneIdentifier = null)
    {
        $this->blockValidator->validatePosition($position, 'position');

        if ($zoneIdentifier !== null) {
            $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

            if (!$this->layoutHandler->zoneExists($block->getLayoutId(), $zoneIdentifier, $block->getStatus())) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }
        }

        if ($block->getStatus() !== Layout::STATUS_DRAFT) {
            throw new BadStateException('block', 'Only blocks in draft status can be moved.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            if ($zoneIdentifier === null || $zoneIdentifier === $block->getZoneIdentifier()) {
                $movedBlock = $this->blockHandler->moveBlock(
                    $block->getId(),
                    $block->getStatus(),
                    $position
                );
            } else {
                $movedBlock = $this->blockHandler->moveBlockToZone(
                    $block->getId(),
                    $block->getStatus(),
                    $zoneIdentifier,
                    $position
                );
            }
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->blockMapper->mapBlock($movedBlock);
    }

    /**
     * Deletes a specified block.
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function deleteBlock(Block $block)
    {
        if ($block->getStatus() !== Layout::STATUS_DRAFT) {
            throw new BadStateException('block', 'Only blocks in draft status can be deleted.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->blockHandler->deleteBlock(
                $block->getId(),
                $block->getStatus()
            );

        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Adds the collection to the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *                                                              If collection with specified identifier already exists within the block
     *                                                              If specified collection already exists within the block
     */
    public function addCollectionToBlock(Block $block, Collection $collection, $identifier)
    {
        if ($block->getStatus() !== Layout::STATUS_DRAFT && $block->getStatus() !== Layout::STATUS_TEMPORARY_DRAFT) {
            throw new BadStateException('block', 'Only blocks in (temporary) draft status can be updated.');
        }

        if ($this->blockHandler->collectionExists($block->getId(), $block->getStatus(), $collection->getId())) {
            throw new BadStateException('collection', 'Specified collection already exists in block.');
        }

        if ($this->blockHandler->collectionIdentifierExists($block->getId(), $block->getStatus(), $identifier)) {
            throw new BadStateException('identifier', 'Specified collection identifier already exists in block.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->blockHandler->addCollectionToBlock(
                $block->getId(),
                $block->getStatus(),
                $collection->getId(),
                $identifier
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Removes the collection from the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *                                                              If specified collection does not exist within the block
     */
    public function removeCollectionFromBlock(Block $block, Collection $collection)
    {
        if ($block->getStatus() !== Layout::STATUS_DRAFT && $block->getStatus() !== Layout::STATUS_TEMPORARY_DRAFT) {
            throw new BadStateException('block', 'Only blocks in (temporary) draft status can be updated.');
        }

        if (!$this->blockHandler->collectionExists($block->getId(), $block->getStatus(), $collection->getId())) {
            throw new BadStateException('collection', 'Specified collection does not exist in block.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->blockHandler->removeCollectionFromBlock(
                $block->getId(),
                $block->getStatus(),
                $collection->getId()
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Creates a new block create struct.
     *
     * @param string $definitionIdentifier
     * @param string $viewType
     *
     * @return \Netgen\BlockManager\API\Values\BlockCreateStruct
     */
    public function newBlockCreateStruct($definitionIdentifier, $viewType)
    {
        return new BlockCreateStruct(
            array(
                'definitionIdentifier' => $definitionIdentifier,
                'viewType' => $viewType,
            )
        );
    }

    /**
     * Creates a new block update struct.
     *
     * @return \Netgen\BlockManager\API\Values\BlockUpdateStruct
     */
    public function newBlockUpdateStruct()
    {
        return new BlockUpdateStruct();
    }
}
