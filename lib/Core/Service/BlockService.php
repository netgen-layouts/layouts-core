<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Service\Validator\BlockValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Service\Mapper\BlockMapper;
use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Exception\BadStateException;
use Exception;

class BlockService implements BlockServiceInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\Validator\BlockValidator
     */
    protected $blockValidator;

    /**
     * @var \Netgen\BlockManager\API\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\Validator\BlockValidator $blockValidator
     * @param \Netgen\BlockManager\API\Service\Mapper\BlockMapper $blockMapper
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
    }

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
    public function loadBlock($blockId, $status = Layout::STATUS_PUBLISHED)
    {
        if (!is_int($blockId) && !is_string($blockId)) {
            throw new InvalidArgumentException('blockId', 'Value must be an integer or a string.');
        }

        if (empty($blockId)) {
            throw new InvalidArgumentException('blockId', 'Value must not be empty.');
        }

        return $this->blockMapper->mapBlock(
            $this->persistenceHandler->getLayoutHandler()->loadBlock(
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If provided zone identifier or position have an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout is not in draft status
     *                                                              If zone does not exist in the layout
     *                                                              If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, Layout $layout, $zoneIdentifier, $position = null)
    {
        if (!is_string($zoneIdentifier) || empty($zoneIdentifier)) {
            throw new InvalidArgumentException('zoneIdentifier', 'The value needs to be a non empty string.');
        }

        if ($position !== null && !is_int($position)) {
            throw new InvalidArgumentException('position', 'Value must be an integer.');
        }

        if ($layout->getStatus() !== Layout::STATUS_DRAFT) {
            throw new BadStateException('layout', 'Blocks can only be created in draft layouts.');
        }

        if ($blockCreateStruct->name === null) {
            $blockCreateStruct->name = '';
        }

        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdBlock = $this->persistenceHandler->getLayoutHandler()->createBlock(
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

        if ($blockUpdateStruct->viewType === null) {
            $blockUpdateStruct->viewType = $block->getViewType();
        }

        if ($blockUpdateStruct->name === null) {
            $blockUpdateStruct->name = $block->getName();
        }

        // Merging the existing parameter array and those to be updated.
        // Excess parameters should be handled by validation.
        $blockUpdateStruct->setParameters(
            $blockUpdateStruct->getParameters() + $block->getParameters()
        );

        $this->blockValidator->validateBlockUpdateStruct($block, $blockUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedBlock = $this->persistenceHandler->getLayoutHandler()->updateBlock(
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If provided zone identifier has an invalid or empty value
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
            if (!is_string($zoneIdentifier) || empty($zoneIdentifier)) {
                throw new InvalidArgumentException('zoneIdentifier', 'Zone identifier must be a non empty string.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedBlock = $this->persistenceHandler->getLayoutHandler()->copyBlock(
                $block->getId(),
                $block->getStatus(),
                $zoneIdentifier
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If provided position or zone identifier have an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in is not in draft status
     *                                                              If zone does not exist in the layout
     *                                                              If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(Block $block, $position, $zoneIdentifier = null)
    {
        if (!is_int($position)) {
            throw new InvalidArgumentException('position', 'Value must be an integer.');
        }

        if ($zoneIdentifier !== null) {
            if (!is_string($zoneIdentifier) || empty($zoneIdentifier)) {
                throw new InvalidArgumentException('zoneIdentifier', 'Zone identifier must be a non empty string.');
            }
        }

        if ($block->getStatus() !== Layout::STATUS_DRAFT) {
            throw new BadStateException('block', 'Only blocks in draft status can be moved.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            if ($zoneIdentifier === null || $zoneIdentifier === $block->getZoneIdentifier()) {
                $movedBlock = $this->persistenceHandler->getLayoutHandler()->moveBlock(
                    $block->getId(),
                    $block->getStatus(),
                    $position
                );
            } else {
                $movedBlock = $this->persistenceHandler->getLayoutHandler()->moveBlockToZone(
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
            $this->persistenceHandler->getLayoutHandler()->deleteBlock(
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
