<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Service\CollectionService as CollectionServiceInterface;
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
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

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
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $blockValidator
     * @param \Netgen\BlockManager\Core\Service\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(
        CollectionServiceInterface $collectionService,
        BlockValidator $blockValidator,
        BlockMapper $blockMapper,
        Handler $persistenceHandler
    ) {
        $this->collectionService = $collectionService;
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
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Page\CollectionReference[]
     */
    public function loadBlockCollections(Block $block)
    {
        $persistenceCollections = $this->blockHandler->loadBlockCollections(
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
        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($position !== null) {
            $this->blockValidator->validatePosition($position, 'position');
        }

        if (!$this->layoutHandler->zoneExists($layout->getId(), $zoneIdentifier, $layout->getStatus())) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
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
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function updateBlock(Block $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(Block $block, $zoneIdentifier = null)
    {
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

            $copiedBlock = $this->blockMapper->mapBlock($copiedBlock);

            $collectionReferences = $this->blockHandler->loadBlockCollections(
                $block->getId(),
                $block->getStatus()
            );

            foreach ($collectionReferences as $collectionReference) {
                $collection = $this->collectionService->loadCollection(
                    $collectionReference->collectionId
                );

                if ($collection->getType() !== Collection::TYPE_NAMED) {
                    $newCollection = $this->collectionService->copyCollection($collection);
                } else {
                    $newCollection = $collection;
                }

                $this->blockHandler->addCollectionToBlock(
                    $copiedBlock->getId(),
                    $copiedBlock->getStatus(),
                    $newCollection->getId(),
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

        return $copiedBlock;
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
        $this->blockValidator->validatePosition($position, 'position');

        if ($zoneIdentifier !== null) {
            $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

            if (!$this->layoutHandler->zoneExists($block->getLayoutId(), $zoneIdentifier, $block->getStatus())) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }
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
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function deleteBlock(Block $block)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            $this->blockHandler->deleteBlock(
                $block->getId(),
                $block->getStatus()
            );

            $collectionReferences = $this->blockHandler->loadBlockCollections(
                $block->getId(),
                $block->getStatus()
            );

            foreach ($collectionReferences as $collectionReference) {
                $collection = $this->collectionService->loadCollection(
                    $collectionReference->collectionId
                );

                $this->blockHandler->removeCollectionFromBlock(
                    $block->getId(),
                    $block->getStatus(),
                    $collection->getId()
                );

                if ($collection->getType() !== Collection::TYPE_NAMED) {
                    $this->collectionService->deleteCollection($collection, true);
                }
            }
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
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection with specified identifier already exists within the block
     *                                                              If specified collection already exists within the block
     */
    public function addCollectionToBlock(Block $block, Collection $collection, $identifier, $offset = 0, $limit = null)
    {
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
                $identifier,
                $offset,
                $limit
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If specified collection does not exist within the block
     */
    public function removeCollectionFromBlock(Block $block, Collection $collection)
    {
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
