<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutDraft;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\BlockDraft;
use Netgen\BlockManager\Exception\BadStateException;
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
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

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
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     */
    public function __construct(
        BlockValidator $blockValidator,
        BlockMapper $blockMapper,
        Handler $persistenceHandler,
        LayoutTypeRegistryInterface $layoutTypeRegistry
    ) {
        $this->blockValidator = $blockValidator;
        $this->blockMapper = $blockMapper;
        $this->persistenceHandler = $persistenceHandler;
        $this->layoutTypeRegistry = $layoutTypeRegistry;

        $this->blockHandler = $persistenceHandler->getBlockHandler();
        $this->layoutHandler = $persistenceHandler->getLayoutHandler();
        $this->collectionHandler = $persistenceHandler->getCollectionHandler();
    }

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function loadBlock($blockId)
    {
        $this->blockValidator->validateId($blockId, 'blockId');

        return $this->blockMapper->mapBlock(
            $this->blockHandler->loadBlock(
                $blockId,
                Layout::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a block draft with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\BlockDraft
     */
    public function loadBlockDraft($blockId)
    {
        $this->blockValidator->validateId($blockId, 'blockId');

        return $this->blockMapper->mapBlock(
            $this->blockHandler->loadBlock(
                $blockId,
                Layout::STATUS_DRAFT
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
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone does not exist in the layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\BlockDRaft
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, LayoutDraft $layout, $zoneIdentifier, $position = null)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Layout::STATUS_DRAFT);

        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier', true);
        $this->blockValidator->validatePosition($position, 'position');
        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        if (!$this->isBlockAllowedWithinZone($blockCreateStruct->definitionIdentifier, $persistenceLayout->type, $zoneIdentifier)) {
            throw new BadStateException('zoneIdentifier', 'Block cannot be created in specified zone.');
        }

        if (!$this->layoutHandler->zoneExists($persistenceLayout->id, $zoneIdentifier, $persistenceLayout->status)) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
        }

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
            $collectionCreateStruct->type = Collection::TYPE_MANUAL;

            $createdCollection = $this->collectionHandler->createCollection(
                $collectionCreateStruct,
                $persistenceLayout->status
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
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\Page\BlockDraft
     */
    public function updateBlock(BlockDraft $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Layout::STATUS_DRAFT);

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
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone does not exist in the layout
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\BlockDraft
     */
    public function copyBlock(BlockDraft $block, $zoneIdentifier = null)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Layout::STATUS_DRAFT);
        $persistenceLayout = $this->layoutHandler->loadLayout($block->getLayoutId(), Layout::STATUS_DRAFT);

        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($zoneIdentifier !== null) {
            if (!$this->layoutHandler->zoneExists($persistenceBlock->layoutId, $zoneIdentifier, $persistenceBlock->status)) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }

            if (!$this->isBlockAllowedWithinZone($block->getDefinitionIdentifier(), $persistenceLayout->type, $zoneIdentifier)) {
                throw new BadStateException('zoneIdentifier', 'Block cannot be placed in specified zone.');
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
                $newCollectionId = $collectionReference->collectionId;

                if (!$this->collectionHandler->isNamedCollection($collectionReference->collectionId, $collectionReference->collectionStatus)) {
                    $newCollectionId = $this->collectionHandler->copyCollection(
                        $collectionReference->collectionId,
                        $persistenceBlock->status
                    );
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
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param int $position
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone does not exist in the layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\BlockDraft
     */
    public function moveBlock(BlockDraft $block, $position, $zoneIdentifier = null)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Layout::STATUS_DRAFT);
        $persistenceLayout = $this->layoutHandler->loadLayout($block->getLayoutId(), Layout::STATUS_DRAFT);

        $this->blockValidator->validatePosition($position, 'position', true);
        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($zoneIdentifier !== null) {
            if (!$this->layoutHandler->zoneExists($persistenceBlock->layoutId, $zoneIdentifier, $persistenceBlock->status)) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }

            if (!$this->isBlockAllowedWithinZone($block->getDefinitionIdentifier(), $persistenceLayout->type, $zoneIdentifier)) {
                throw new BadStateException('zoneIdentifier', 'Block cannot be placed in specified zone.');
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
     * Restores the specified block from the published status. Zone and position are kept as is.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block does not have a published status
     *
     * @return \Netgen\BlockManager\API\Values\Page\BlockDraft
     */
    public function restoreBlock(BlockDraft $block)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Layout::STATUS_DRAFT);

        try {
            $publishedBlock = $this->blockHandler->loadBlock($persistenceBlock->id, Layout::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            throw new BadStateException('block', 'Block cannot be restored as it does not have a published status.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedBlock = $this->blockHandler->updateBlock(
                $persistenceBlock->id,
                $persistenceBlock->status,
                new BlockUpdateStruct(
                    array(
                        'name' => $publishedBlock->name,
                        'viewType' => $publishedBlock->viewType,
                        'itemViewType' => $publishedBlock->itemViewType,
                        'parameters' => $publishedBlock->parameters,
                    )
                )
            );

            $this->blockHandler->deleteBlockCollections($persistenceBlock->id, $persistenceBlock->status);

            $this->blockHandler->createBlockCollectionsStatus(
                $publishedBlock->id,
                $publishedBlock->status,
                $persistenceBlock->status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->blockMapper->mapBlock($updatedBlock);
    }

    /**
     * Deletes a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     */
    public function deleteBlock(BlockDraft $block)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Layout::STATUS_DRAFT);

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
     * @param string $itemViewType
     *
     * @return \Netgen\BlockManager\API\Values\BlockCreateStruct
     */
    public function newBlockCreateStruct($definitionIdentifier, $viewType, $itemViewType)
    {
        return new BlockCreateStruct(
            array(
                'definitionIdentifier' => $definitionIdentifier,
                'viewType' => $viewType,
                'itemViewType' => $itemViewType,
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

    /**
     * Returns if the block is allowed within the zone.
     *
     * @param string $definitionIdentifier
     * @param string $layoutType
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    protected function isBlockAllowedWithinZone($definitionIdentifier, $layoutType, $zoneIdentifier)
    {
        if (!$this->layoutTypeRegistry->hasLayoutType($layoutType)) {
            return true;
        }

        $layoutType = $this->layoutTypeRegistry->getLayoutType($layoutType);
        if (!$layoutType->hasZone($zoneIdentifier)) {
            return true;
        }

        $zone = $layoutType->getZone($zoneIdentifier);

        return $zone->isBlockDefinitionAllowed($definitionIdentifier);
    }
}
