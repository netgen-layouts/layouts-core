<?php

namespace Netgen\BlockManager\Core\Service;

use Exception;
use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\ConfigMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;

class BlockService extends Service implements BlockServiceInterface
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\BlockValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder
     */
    protected $structBuilder;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    protected $configMapper;

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
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\BlockMapper $mapper
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder $structBuilder
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper $configMapper
     */
    public function __construct(
        Handler $persistenceHandler,
        BlockValidator $validator,
        BlockMapper $mapper,
        BlockStructBuilder $structBuilder,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;

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
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlock($blockId)
    {
        $this->validator->validateId($blockId, 'blockId');

        $block = $this->blockHandler->loadBlock($blockId, Value::STATUS_PUBLISHED);

        if (empty($block->parentId)) {
            // We do not allow loading root zone blocks
            throw new NotFoundException('block', $blockId);
        }

        return $this->mapper->mapBlock($block);
    }

    /**
     * Loads a block draft with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlockDraft($blockId)
    {
        $this->validator->validateId($blockId, 'blockId');

        $block = $this->blockHandler->loadBlock($blockId, Value::STATUS_DRAFT);

        if (empty($block->parentId)) {
            // We do not allow loading root zone blocks
            throw new NotFoundException('block', $blockId);
        }

        return $this->mapper->mapBlock($block);
    }

    /**
     * Loads all blocks belonging to provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function loadZoneBlocks(Zone $zone)
    {
        $persistenceZone = $this->layoutHandler->loadZone(
            $zone->getLayoutId(),
            $zone->getStatus(),
            $zone->getIdentifier()
        );

        $rootBlock = $this->blockHandler->loadBlock(
            $persistenceZone->rootBlockId,
            $persistenceZone->status
        );

        $persistenceBlocks = $this->blockHandler->loadChildBlocks($rootBlock);

        $blocks = array();
        foreach ($persistenceBlocks as $persistenceBlock) {
            $blocks[] = $this->mapper->mapBlock($persistenceBlock);
        }

        return $blocks;
    }

    /**
     * Returns if provided block has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
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
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference
     */
    public function loadCollectionReference(Block $block, $identifier)
    {
        $this->validator->validateIdentifier($identifier, null, true);

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());

        return $this->mapper->mapCollectionReference(
            $persistenceBlock,
            $this->blockHandler->loadCollectionReference(
                $persistenceBlock,
                $identifier
            )
        );
    }

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block)
    {
        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());
        $persistenceCollections = $this->blockHandler->loadCollectionReferences($persistenceBlock);

        $collections = array();
        foreach ($persistenceCollections as $persistenceCollection) {
            $collections[] = $this->mapper->mapCollectionReference(
                $persistenceBlock,
                $persistenceCollection
            );
        }

        return $collections;
    }

    /**
     * Creates a block in specified block and placeholder.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is not a draft
     *                                                          If target block is not a container
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, Block $targetBlock, $placeholder, $position = null)
    {
        if ($targetBlock->isPublished()) {
            throw new BadStateException('targetBlock', 'Blocks can only be created in blocks in draft status.');
        }

        $this->validator->validateIdentifier($placeholder, 'placeholder', true);
        $this->validator->validatePosition($position, 'position');
        $this->validator->validateBlockCreateStruct($blockCreateStruct);

        $targetBlockDefinition = $targetBlock->getDefinition();

        if (!$targetBlockDefinition instanceof ContainerDefinitionInterface) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!$targetBlockDefinition->hasPlaceholder($placeholder)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($blockCreateStruct->definition instanceof ContainerDefinitionInterface) {
            throw new BadStateException('blockCreateStruct', 'Containers cannot be placed inside containers.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($targetBlock->getId(), Value::STATUS_DRAFT);

        return $this->internalCreateBlock($blockCreateStruct, $persistenceBlock, $placeholder, $position);
    }

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function createBlockInZone(APIBlockCreateStruct $blockCreateStruct, Layout $layout, $zoneIdentifier, $position = null)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Blocks can only be created in layouts in draft status.');
        }

        $this->validator->validateIdentifier($zoneIdentifier, 'zoneIdentifier', true);

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($persistenceLayout->id, Value::STATUS_DRAFT, $zoneIdentifier);

        $this->validator->validatePosition($position, 'position');
        $this->validator->validateBlockCreateStruct($blockCreateStruct);

        if (
            !$this->isBlockAllowedWithinZone(
                $blockCreateStruct->definition,
                $layout->getLayoutType(),
                $persistenceZone->identifier
            )
        ) {
            throw new BadStateException('zone', 'Block is not allowed in specified zone.');
        }

        $rootBlock = $this->blockHandler->loadBlock(
            $persistenceZone->rootBlockId,
            $persistenceZone->status
        );

        return $this->internalCreateBlock($blockCreateStruct, $rootBlock, 'root', $position);
    }

    /**
     * Updates a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function updateBlock(Block $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be updated.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);

        $this->validator->validateBlockUpdateStruct($block, $blockUpdateStruct);

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
                            $block->getDefinition(),
                            $blockUpdateStruct->getParameterValues()
                        ) + $persistenceBlock->parameters,
                        'config' => array_replace_recursive(
                            $persistenceBlock->config,
                            $this->configMapper->serializeValues(
                                $block->getConfigCollection()->getConfigType(),
                                $blockUpdateStruct->getConfigStructs()
                            )
                        ),
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapBlock($updatedBlock);
    }

    /**
     * Updates a specified collection reference.
     *
     * @param \Netgen\BlockManager\API\Values\Block\CollectionReference $collectionReference
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference
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

        return $this->mapper->mapCollectionReference($persistenceBlock, $updatedReference);
    }

    /**
     * Copies a block to a specified target block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If source or target block is not a draft
     *                                                          If target block is not a container
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If target block is within the provided block
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function copyBlock(Block $block, Block $targetBlock, $placeholder)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be copied.');
        }

        if ($targetBlock->isPublished()) {
            throw new BadStateException('targetBlock', 'You can only copy blocks to draft blocks.');
        }

        $this->validator->validateIdentifier($placeholder, 'placeholder', true);

        $targetBlockDefinition = $targetBlock->getDefinition();

        if (!$targetBlockDefinition instanceof ContainerDefinitionInterface) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!$targetBlockDefinition->hasPlaceholder($placeholder)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($block->getDefinition() instanceof ContainerDefinitionInterface) {
            throw new BadStateException('block', 'Containers cannot be placed inside containers.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceTargetBlock = $this->blockHandler->loadBlock($targetBlock->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedBlock = $this->blockHandler->copyBlock($persistenceBlock, $persistenceTargetBlock, $placeholder);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapBlock($copiedBlock);
    }

    /**
     * Copies a block to a specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or layout are not drafts
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function copyBlockToZone(Block $block, Layout $layout, $zoneIdentifier)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be copied.');
        }

        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'You can only copy blocks in draft layouts.');
        }

        $this->validator->validateIdentifier($zoneIdentifier, 'zoneIdentifier', true);

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($persistenceLayout->id, Value::STATUS_DRAFT, $zoneIdentifier);

        if (!$this->isBlockAllowedWithinZone($block->getDefinition(), $layout->getLayoutType(), $persistenceZone->identifier)) {
            throw new BadStateException('zoneIdentifier', 'Block is not allowed in specified zone.');
        }

        $rootBlock = $this->blockHandler->loadBlock($persistenceZone->rootBlockId, $persistenceZone->status);

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedBlock = $this->blockHandler->copyBlock($persistenceBlock, $rootBlock, 'root');
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapBlock($copiedBlock);
    }

    /**
     * Moves a block to specified target block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If source or target block is not a draft
     *                                                          If target block is not a container
     *                                                          If placeholder does not exist in the target block
     *                                                          If new block is a container
     *                                                          If target block is within the provided block
     *                                                          If provided position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be moved.');
        }

        if ($targetBlock->isPublished()) {
            throw new BadStateException('targetBlock', 'You can only move blocks to draft blocks.');
        }

        $this->validator->validateIdentifier($placeholder, 'placeholder', true);
        $this->validator->validatePosition($position, 'position', true);

        $targetBlockDefinition = $targetBlock->getDefinition();

        if (!$targetBlockDefinition instanceof ContainerDefinitionInterface) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!$targetBlockDefinition->hasPlaceholder($placeholder)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($block->getDefinition() instanceof ContainerDefinitionInterface) {
            throw new BadStateException('block', 'Containers cannot be placed inside containers.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceTargetBlock = $this->blockHandler->loadBlock($targetBlock->getId(), Value::STATUS_DRAFT);

        return $this->internalMoveBlock($persistenceBlock, $persistenceTargetBlock, $placeholder, $position);
    }

    /**
     * Moves a block to specified position inside the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or layout are not drafts
     *                                                          If zone is in a different layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function moveBlockToZone(Block $block, Layout $layout, $zoneIdentifier, $position)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be moved.');
        }

        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'You can only move blocks in draft layouts.');
        }

        $this->validator->validateIdentifier($zoneIdentifier, 'zoneIdentifier', true);
        $this->validator->validatePosition($position, 'position', true);

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($persistenceLayout->id, Value::STATUS_DRAFT, $zoneIdentifier);

        if ($persistenceBlock->layoutId !== $persistenceLayout->id) {
            throw new BadStateException('layout', 'You can only move block to zone in the same layout.');
        }

        if (!$this->isBlockAllowedWithinZone($block->getDefinition(), $layout->getLayoutType(), $persistenceZone->identifier)) {
            throw new BadStateException('zoneIdentifier', 'Block is not allowed in specified zone.');
        }

        $rootBlock = $this->blockHandler->loadBlock($persistenceZone->rootBlockId, $persistenceZone->status);

        return $this->internalMoveBlock($persistenceBlock, $rootBlock, 'root', $position);
    }

    /**
     * Restores the specified block from the published status. Position of the block is kept as is.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If block does not have a published status
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
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

        return $this->mapper->mapBlock($updatedBlock);
    }

    /**
     * Deletes a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
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
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    public function newBlockCreateStruct(BlockDefinitionInterface $blockDefinition)
    {
        return $this->structBuilder->newBlockCreateStruct($blockDefinition);
    }

    /**
     * Creates a new block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct
     */
    public function newBlockUpdateStruct(Block $block = null)
    {
        return $this->structBuilder->newBlockUpdateStruct($block);
    }

    /**
     * Creates a block. Internal method unifying creating a block in a zone and a parent block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    protected function internalCreateBlock(
        APIBlockCreateStruct $blockCreateStruct,
        PersistenceBlock $targetBlock,
        $placeholder,
        $position = null
    ) {
        $this->persistenceHandler->beginTransaction();

        try {
            $createdBlock = $this->blockHandler->createBlock(
                new BlockCreateStruct(
                    array(
                        'layoutId' => $targetBlock->layoutId,
                        'status' => $targetBlock->status,
                        'position' => $position,
                        'definitionIdentifier' => $blockCreateStruct->definition->getIdentifier(),
                        'viewType' => $blockCreateStruct->viewType,
                        'itemViewType' => $blockCreateStruct->itemViewType,
                        'name' => $blockCreateStruct->name,
                        'placeholderParameters' => array(),
                        'parameters' => $this->parameterMapper->serializeValues(
                            $blockCreateStruct->definition,
                            $blockCreateStruct->getParameterValues()
                        ),
                        'config' => $this->configMapper->serializeValues(
                            'block',
                            $blockCreateStruct->getConfigStructs()
                        ),
                    )
                ),
                $targetBlock,
                $placeholder
            );

            if ($blockCreateStruct->definition->hasCollection()) {
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

        return $this->mapper->mapBlock($createdBlock);
    }

    /**
     * Moves a block. Internal method unifying moving a block to a zone and to a parent block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    protected function internalMoveBlock(PersistenceBlock $block, PersistenceBlock $targetBlock, $placeholder, $position)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            if ($block->parentId === $targetBlock->id && $block->placeholder === $placeholder) {
                $movedBlock = $this->blockHandler->moveBlockToPosition($block, $position);
            } else {
                $movedBlock = $this->blockHandler->moveBlock(
                    $block,
                    $targetBlock,
                    $placeholder,
                    $position
                );
            }
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapBlock($movedBlock);
    }

    /**
     * Returns if the block is allowed within the zone.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $definition
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     * @param string $zoneIdentifier
     *
     * @return bool
     *
     * @internal param string $definitionIdentifier
     */
    protected function isBlockAllowedWithinZone(BlockDefinitionInterface $definition, LayoutType $layoutType, $zoneIdentifier)
    {
        if (!$layoutType->hasZone($zoneIdentifier)) {
            return true;
        }

        $zone = $layoutType->getZone($zoneIdentifier);

        return $zone->isBlockDefinitionAllowed($definition->getIdentifier());
    }
}
