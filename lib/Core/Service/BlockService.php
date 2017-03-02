<?php

namespace Netgen\BlockManager\Core\Service;

use Exception;
use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
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
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $blockValidator
     * @param \Netgen\BlockManager\Core\Service\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     */
    public function __construct(
        Handler $persistenceHandler,
        BlockValidator $blockValidator,
        BlockMapper $blockMapper,
        ParameterMapper $parameterMapper,
        LayoutTypeRegistryInterface $layoutTypeRegistry
    ) {
        parent::__construct($persistenceHandler);

        $this->blockValidator = $blockValidator;
        $this->blockMapper = $blockMapper;
        $this->parameterMapper = $parameterMapper;
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
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlock($blockId)
    {
        $this->blockValidator->validateId($blockId, 'blockId');

        $block = $this->blockHandler->loadBlock($blockId, Value::STATUS_PUBLISHED);

        if (empty($block->parentId)) {
            // We do not allow loading root zone blocks
            throw new NotFoundException('block', $blockId);
        }

        return $this->blockMapper->mapBlock($block);
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
        $this->blockValidator->validateId($blockId, 'blockId');

        $block = $this->blockHandler->loadBlock($blockId, Value::STATUS_DRAFT);

        if (empty($block->parentId)) {
            // We do not allow loading root zone blocks
            throw new NotFoundException('block', $blockId);
        }

        return $this->blockMapper->mapBlock($block);
    }

    /**
     * Loads all blocks belonging to provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]]
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
            $blocks[] = $this->blockMapper->mapBlock($persistenceBlock);
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
        $this->blockValidator->validateIdentifier($identifier, null, true);

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), $block->getStatus());

        return $this->blockMapper->mapCollectionReference(
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
            $collections[] = $this->blockMapper->mapCollectionReference(
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

        $this->blockValidator->validateIdentifier($placeholder, 'placeholder', true);
        $this->blockValidator->validatePosition($position, 'position');
        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        if (!$targetBlock->getDefinition()->isContainer()) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!$targetBlock->getDefinition()->hasPlaceholder($placeholder)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($blockCreateStruct->definition->isContainer()) {
            throw new BadStateException('blockCreateStruct', 'Containers cannot be placed inside containers.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($targetBlock->getId(), Value::STATUS_DRAFT);

        return $this->internalCreateBlock($blockCreateStruct, $persistenceBlock, $placeholder, $position);
    }

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function createBlockInZone(APIBlockCreateStruct $blockCreateStruct, Zone $zone, $position = null)
    {
        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'Blocks can only be created in zones in draft status.');
        }

        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());
        $persistenceLayout = $this->layoutHandler->loadLayout($zone->getLayoutId(), Value::STATUS_DRAFT);

        $this->blockValidator->validatePosition($position, 'position');
        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        if (
            !$this->isBlockAllowedWithinZone(
                $blockCreateStruct->definition->getIdentifier(),
                $persistenceLayout->type,
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
                            $block->getDefinition(),
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

        return $this->blockMapper->mapCollectionReference($persistenceBlock, $updatedReference);
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

        $this->blockValidator->validateIdentifier($placeholder, 'placeholder', true);

        if (!$targetBlock->getDefinition()->isContainer()) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!$targetBlock->getDefinition()->hasPlaceholder($placeholder)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($block->getDefinition()->isContainer()) {
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

        return $this->blockMapper->mapBlock($copiedBlock);
    }

    /**
     * Copies a block to a specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function copyBlockToZone(Block $block, Zone $zone)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be copied.');
        }

        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'You can only copy blocks to draft zones.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());
        $persistenceLayout = $this->layoutHandler->loadLayout($zone->getLayoutId(), Value::STATUS_DRAFT);

        if (!$this->isBlockAllowedWithinZone($persistenceBlock->definitionIdentifier, $persistenceLayout->type, $persistenceZone->identifier)) {
            throw new BadStateException('zone', 'Block is not allowed in specified zone.');
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

        return $this->blockMapper->mapBlock($copiedBlock);
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

        $this->blockValidator->validateIdentifier($placeholder, 'placeholder', true);
        $this->blockValidator->validatePosition($position, 'position', true);

        if (!$targetBlock->getDefinition()->isContainer()) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!$targetBlock->getDefinition()->hasPlaceholder($placeholder)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($block->getDefinition()->isContainer()) {
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
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If zone is in a different layout
     *                                                          If provided position is out of range
     *                                                          If block cannot be placed in specified zone
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function moveBlockToZone(Block $block, Zone $zone, $position)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be moved.');
        }

        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'You can only move blocks to draft zones.');
        }

        $this->blockValidator->validatePosition($position, 'position', true);

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());
        $persistenceLayout = $this->layoutHandler->loadLayout($zone->getLayoutId(), Value::STATUS_DRAFT);

        if ($persistenceBlock->layoutId !== $persistenceZone->layoutId) {
            throw new BadStateException('zone', 'You can only move block to zone in the same layout.');
        }

        if (!$this->isBlockAllowedWithinZone($persistenceBlock->definitionIdentifier, $persistenceLayout->type, $persistenceZone->identifier)) {
            throw new BadStateException('zone', 'Block is not allowed in specified zone.');
        }

        $rootBlock = $this->blockHandler->loadBlock($persistenceZone->rootBlockId, $persistenceZone->status);

        return $this->internalMoveBlock($persistenceBlock, $rootBlock, 'root', $position);
    }

    /**
     * Restores the specified block from the published status. Zone and position are kept as is.
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

        return $this->blockMapper->mapBlock($updatedBlock);
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
        $config = $blockDefinition->getConfig();

        $viewTypeIdentifier = $config->getViewTypeIdentifiers()[0];
        $viewType = $config->getViewType($viewTypeIdentifier);
        $itemViewTypeIdentifier = $viewType->getItemViewTypeIdentifiers()[0];

        $blockCreateStruct = new APIBlockCreateStruct(
            array(
                'definition' => $blockDefinition,
                'viewType' => $viewTypeIdentifier,
                'itemViewType' => $itemViewTypeIdentifier,
            )
        );

        $blockCreateStruct->fillValues($blockDefinition);

        return $blockCreateStruct;
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
        $blockUpdateStruct = new APIBlockUpdateStruct();

        if (!$block instanceof Block) {
            return $blockUpdateStruct;
        }

        $blockUpdateStruct->viewType = $block->getViewType();
        $blockUpdateStruct->itemViewType = $block->getItemViewType();
        $blockUpdateStruct->name = $block->getName();

        $blockDefinition = $block->getDefinition();

        $blockUpdateStruct->fillValues(
            $blockDefinition,
            $block->getParameters(),
            false
        );

        return $blockUpdateStruct;
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

        $placeholderParameters = array();
        if ($blockCreateStruct->definition->isContainer()) {
            foreach ($blockCreateStruct->definition->getPlaceholders() as $placeholderDefinition) {
                $placeholderIdentifier = $placeholderDefinition->getIdentifier();

                $placeholderParameterValues = array();
                if ($blockCreateStruct->hasPlaceholderStruct($placeholderIdentifier)) {
                    $placeholderParameterValues = $blockCreateStruct
                        ->getPlaceholderStruct($placeholderIdentifier)
                        ->getParameterValues();
                }

                $placeholderParameters[$placeholderIdentifier] = $this->parameterMapper->serializeValues(
                    $placeholderDefinition,
                    $placeholderParameterValues
                );
            }
        }

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
                        'placeholderParameters' => $placeholderParameters,
                        'parameters' => $this->parameterMapper->serializeValues(
                            $blockCreateStruct->definition,
                            $blockCreateStruct->getParameterValues()
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

        return $this->blockMapper->mapBlock($createdBlock);
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

        return $this->blockMapper->mapBlock($movedBlock);
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
