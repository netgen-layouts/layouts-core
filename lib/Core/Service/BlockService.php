<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\Values\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\CollectionCreateStruct;
use Exception;
use Netgen\BlockManager\Persistence\Values\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\CollectionReferenceUpdateStruct;

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
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

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
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(
        BlockValidator $blockValidator,
        BlockMapper $blockMapper,
        ParameterMapper $parameterMapper,
        Handler $persistenceHandler,
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    ) {
        $this->blockValidator = $blockValidator;
        $this->blockMapper = $blockMapper;
        $this->parameterMapper = $parameterMapper;
        $this->persistenceHandler = $persistenceHandler;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;

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
                Value::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function loadBlockDraft($blockId)
    {
        $this->blockValidator->validateId($blockId, 'blockId');

        return $this->blockMapper->mapBlock(
            $this->blockHandler->loadBlock(
                $blockId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Returns if provided block has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return bool
     */
    public function hasPublishedState(Block $block)
    {
        return $this->blockHandler->blockExists($block->getId(), Value::STATUS_PUBLISHED);
    }

    /**
     * Loads the collection reference with specified identifier.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Page\CollectionReference
     */
    public function loadCollectionReference(Block $block, $identifier)
    {
        $this->blockValidator->validateIdentifier($identifier, null, true);

        return $this->blockMapper->mapCollectionReference(
            $this->blockHandler->loadCollectionReference(
                $this->blockHandler->loadBlock(
                    $block->getId(),
                    $block->getStatus()
                ),
                $identifier
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
            $this->blockHandler->loadBlock(
                $block->getId(),
                $block->getStatus()
            )
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If zone does not exist in the layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, Layout $layout, $zoneIdentifier, $position = null)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Blocks can only be created in layouts in draft status.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier', true);
        $this->blockValidator->validatePosition($position, 'position');
        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        if (!$this->layoutHandler->zoneExists($persistenceLayout->id, $persistenceLayout->status, $zoneIdentifier)) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
        }

        if (!$this->isBlockAllowedWithinZone($blockCreateStruct->definitionIdentifier, $persistenceLayout->type, $zoneIdentifier)) {
            throw new BadStateException('zoneIdentifier', 'Block cannot be created in specified zone.');
        }

        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $blockCreateStruct->definitionIdentifier
        );

        $this->persistenceHandler->beginTransaction();

        try {
            $createdBlock = $this->blockHandler->createBlock(
                new BlockCreateStruct(
                    array(
                        'layoutId' => $persistenceLayout->id,
                        'zoneIdentifier' => $zoneIdentifier,
                        'status' => $persistenceLayout->status,
                        'position' => $position,
                        'definitionIdentifier' => $blockCreateStruct->definitionIdentifier,
                        'viewType' => $blockCreateStruct->viewType,
                        'itemViewType' => $blockCreateStruct->itemViewType,
                        'name' => $blockCreateStruct->name,
                        'parameters' => $this->parameterMapper->serializeValues(
                            $blockDefinition,
                            $blockCreateStruct->getParameterValues()
                        ),
                    )
                )
            );

            if ($blockDefinition->hasCollection()) {
                $collectionCreateStruct = new CollectionCreateStruct();
                $collectionCreateStruct->type = Collection::TYPE_MANUAL;

                $createdCollection = $this->collectionHandler->createCollection(
                    new CollectionCreateStruct(
                        array(
                            'status' => Value::STATUS_DRAFT,
                            'type' => Collection::TYPE_MANUAL,
                            'shared' => false,
                        )
                    )
                );

                $this->blockHandler->createCollectionReference(
                    $createdBlock,
                    new CollectionReferenceCreateStruct(
                        array(
                            'identifier' => 'default',
                            'collection' => $createdCollection,
                            'offset' => 0,
                            'limit' => null,
                        )
                    )
                );
            }
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function updateBlock(Block $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be updated.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);

        $this->blockValidator->validateBlockUpdateStruct($block, $blockUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedBlock = $this->blockHandler->updateBlock(
                $persistenceBlock,
                new BlockUpdateStruct(
                    array(
                        'viewType' => $blockUpdateStruct->viewType,
                        'itemViewType' => $blockUpdateStruct->itemViewType,
                        'name' => $blockUpdateStruct->name,
                        'parameters' => $this->parameterMapper->serializeValues(
                            $block->getBlockDefinition(),
                            $blockUpdateStruct->getParameterValues()
                        ) + $persistenceBlock->parameters,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->blockMapper->mapBlock($updatedBlock);
    }

    /**
     * Updates a specified collection reference.
     *
     * @param \Netgen\BlockManager\API\Values\Page\CollectionReference $collectionReference
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Page\CollectionReference
     */
    public function updateCollectionReference(CollectionReference $collectionReference, Collection $collection)
    {
        $block = $collectionReference->getBlock();
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());

        $persistenceCollection = $this->collectionHandler->loadCollection(
            $collection->getId(),
            $collection->getStatus()
        );

        $persistenceCollectionReference = $this->blockHandler->loadCollectionReference(
            $persistenceBlock,
            $collectionReference->getIdentifier()
        );

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedReference = $this->blockHandler->updateCollectionReference(
                $persistenceCollectionReference,
                new CollectionReferenceUpdateStruct(
                    array(
                        'collection' => $persistenceCollection,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->blockMapper->mapCollectionReference($updatedReference);
    }

    /**
     * Copies a specified block. If zone is specified, copied block will be
     * placed in it, otherwise, it will be placed in the same zone where source block is.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If zone does not exist in the layout
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(Block $block, $zoneIdentifier = null)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be copied.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceLayout = $this->layoutHandler->loadLayout($block->getLayoutId(), Value::STATUS_DRAFT);

        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($zoneIdentifier !== null) {
            if (!$this->layoutHandler->zoneExists($persistenceLayout->id, $persistenceLayout->status, $zoneIdentifier)) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }

            if (!$this->isBlockAllowedWithinZone($block->getBlockDefinition()->getIdentifier(), $persistenceLayout->type, $zoneIdentifier)) {
                throw new BadStateException('zoneIdentifier', 'Block cannot be placed in specified zone.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedBlock = $this->blockHandler->copyBlock(
                $persistenceBlock,
                $persistenceLayout,
                $zoneIdentifier !== null ? $zoneIdentifier : $persistenceBlock->zoneIdentifier
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If zone does not exist in the layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(Block $block, $position, $zoneIdentifier = null)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be moved.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceLayout = $this->layoutHandler->loadLayout($block->getLayoutId(), Value::STATUS_DRAFT);

        $this->blockValidator->validatePosition($position, 'position', true);
        $this->blockValidator->validateIdentifier($zoneIdentifier, 'zoneIdentifier');

        if ($zoneIdentifier !== null) {
            if (!$this->layoutHandler->zoneExists($persistenceLayout->id, $persistenceLayout->status, $zoneIdentifier)) {
                throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
            }

            if (!$this->isBlockAllowedWithinZone($block->getBlockDefinition()->getIdentifier(), $persistenceLayout->type, $zoneIdentifier)) {
                throw new BadStateException('zoneIdentifier', 'Block cannot be placed in specified zone.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            if ($zoneIdentifier === null || $zoneIdentifier === $persistenceBlock->zoneIdentifier) {
                $movedBlock = $this->blockHandler->moveBlock(
                    $persistenceBlock,
                    $position
                );
            } else {
                $movedBlock = $this->blockHandler->moveBlockToZone(
                    $persistenceBlock,
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
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If block does not have a published status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function restoreBlock(Block $block)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be restored.');
        }

        $draftBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);

        try {
            $publishedBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            throw new BadStateException('block', 'Block does not have a published status.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedBlock = $this->blockHandler->updateBlock(
                $draftBlock,
                new BlockUpdateStruct(
                    array(
                        'name' => $publishedBlock->name,
                        'viewType' => $publishedBlock->viewType,
                        'itemViewType' => $publishedBlock->itemViewType,
                        'parameters' => $publishedBlock->parameters,
                    )
                )
            );

            $this->blockHandler->deleteBlockCollections(array($draftBlock->id), $draftBlock->status);
            $this->blockHandler->createBlockCollectionsStatus($publishedBlock, $draftBlock->status);
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
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     */
    public function deleteBlock(Block $block)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be deleted.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->blockHandler->deleteBlock($persistenceBlock);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Creates a new block create struct.
     *
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockType $blockType
     *
     * @return \Netgen\BlockManager\API\Values\BlockCreateStruct
     */
    public function newBlockCreateStruct(BlockType $blockType)
    {
        $blockDefinition = $blockType->getBlockDefinition();
        $config = $blockDefinition->getConfig();

        $viewTypeIdentifier = $blockType->getDefaultViewType();
        $itemViewTypeIdentifier = $blockType->getDefaultItemViewType();

        if (!$config->hasViewType($viewTypeIdentifier)) {
            $viewTypeIdentifier = $config->getViewTypeIdentifiers()[0];
        }

        $viewType = $config->getViewType($viewTypeIdentifier);
        if (!$viewType->hasItemViewType($itemViewTypeIdentifier)) {
            $itemViewTypeIdentifier = $viewType->getItemViewTypeIdentifiers()[0];
        }

        $blockCreateStruct = new APIBlockCreateStruct(
            array(
                'definitionIdentifier' => $blockDefinition->getIdentifier(),
                'name' => $blockType->getDefaultName(),
                'viewType' => $viewTypeIdentifier,
                'itemViewType' => $itemViewTypeIdentifier,
            )
        );

        $blockCreateStruct->fillValues(
            $blockDefinition,
            $blockType->getDefaultParameters()
        );

        return $blockCreateStruct;
    }

    /**
     * Creates a new block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\BlockUpdateStruct
     */
    public function newBlockUpdateStruct(Block $block = null)
    {
        $blockUpdateStruct = new APIBlockUpdateStruct();

        if (!$block instanceof Block) {
            return $blockUpdateStruct;
        }

        $blockUpdateStruct->viewType = $block->getViewType();
        $blockUpdateStruct->itemViewType = $block->getItemViewType();
        $blockUpdateStruct->name = $block->getName();

        $blockDefinition = $block->getBlockDefinition();

        $blockUpdateStruct->fillValues(
            $blockDefinition,
            $block->getParameters(),
            false
        );

        return $blockUpdateStruct;
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
        $layoutType = $this->layoutTypeRegistry->getLayoutType($layoutType);
        if (!$layoutType->hasZone($zoneIdentifier)) {
            return true;
        }

        $zone = $layoutType->getZone($zoneIdentifier);

        return $zone->isBlockDefinitionAllowed($definitionIdentifier);
    }
}
