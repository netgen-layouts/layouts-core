<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
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
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

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
        $this->collectionHandler = $persistenceHandler->getCollectionHandler();
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
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Page\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block)
    {
        $persistenceCollections = $this->blockHandler->loadCollectionReferences(
            $block->getId(),
            $block->getStatus()
        );

        $collections = array();
        foreach ($persistenceCollections as $persistenceCollection) {
            $collections[] = $this->blockMapper->mapCollectionReference($persistenceCollection);
        }

        return $collections;
    }

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone does not exist in the layout
     *                                                              If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, Layout $layout, $zoneIdentifier, $position = null)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier', true);

        $this->blockValidator->validatePosition($position, 'position');

        if (!$this->layoutHandler->zoneExists($persistenceLayout->id, $zoneIdentifier, $persistenceLayout->status)) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
        }

        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdBlock = $this->blockHandler->createBlock(
                $blockCreateStruct,
                $persistenceLayout->id,
                $zoneIdentifier,
                $persistenceLayout->status,
                $position
            );

            $collectionCreateStruct = new CollectionCreateStruct();
            $collectionCreateStruct->status = $persistenceLayout->status;

            $createdCollection = $this->collectionHandler->createCollection(
                $collectionCreateStruct,
                Collection::TYPE_MANUAL
            );

            $this->blockHandler->addCollectionToBlock(
                $createdBlock->id,
                $createdBlock->status,
                $createdCollection->id,
                $createdCollection->status,
                'default'
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
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function updateBlock(Block $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());

        $this->blockValidator->validateBlockUpdateStruct($block, $blockUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedBlock = $this->blockHandler->updateBlock(
                $persistenceBlock->id,
                $persistenceBlock->status,
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(Block $block, $zoneIdentifier = null)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());

        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($zoneIdentifier !== null) {
            if (!$this->layoutHandler->zoneExists($persistenceBlock->layoutId, $zoneIdentifier, $persistenceBlock->status)) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedBlock = $this->blockHandler->copyBlock(
                $persistenceBlock->id,
                $persistenceBlock->status,
                $zoneIdentifier !== null ? $zoneIdentifier : $persistenceBlock->zoneIdentifier
            );

            $collectionReferences = $this->blockHandler->loadCollectionReferences(
                $persistenceBlock->id,
                $persistenceBlock->status
            );

            foreach ($collectionReferences as $collectionReference) {
                if (!$this->collectionHandler->isNamedCollection($collectionReference->collectionId, $collectionReference->collectionStatus)) {
                    $newCollectionId = $this->collectionHandler->copyCollection(
                        $collectionReference->collectionId,
                        $persistenceBlock->status
                    );
                } else {
                    $newCollectionId = $collectionReference->collectionId;
                }

                $this->blockHandler->addCollectionToBlock(
                    $copiedBlock->id,
                    $collectionReference->blockStatus,
                    $newCollectionId,
                    $collectionReference->collectionStatus,
                    $collectionReference->identifier,
                    $collectionReference->offset,
                    $collectionReference->limit
                );
            }
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone does not exist in the layout
     *                                                              If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(Block $block, $position, $zoneIdentifier = null)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());

        $this->blockValidator->validatePosition($position, 'position', true);

        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($zoneIdentifier !== null) {
            if (!$this->layoutHandler->zoneExists($persistenceBlock->layoutId, $zoneIdentifier, $persistenceBlock->status)) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            if ($zoneIdentifier === null || $zoneIdentifier === $persistenceBlock->zoneIdentifier) {
                $movedBlock = $this->blockHandler->moveBlock(
                    $persistenceBlock->id,
                    $persistenceBlock->status,
                    $position
                );
            } else {
                $movedBlock = $this->blockHandler->moveBlockToZone(
                    $persistenceBlock->id,
                    $persistenceBlock->status,
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
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function deleteBlock(Block $block)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->blockHandler->deleteBlock(
                $persistenceBlock->id,
                $persistenceBlock->status
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
