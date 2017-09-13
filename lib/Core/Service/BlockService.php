<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Service\LayoutService as APILayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct as APIBlockUpdateStruct;
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
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\TranslationUpdateStruct;
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
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\BlockMapper $mapper
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder $structBuilder
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper $configMapper
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(
        Handler $persistenceHandler,
        BlockValidator $validator,
        BlockMapper $mapper,
        BlockStructBuilder $structBuilder,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        APILayoutService $layoutService
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->layoutService = $layoutService;

        $this->blockHandler = $persistenceHandler->getBlockHandler();
        $this->layoutHandler = $persistenceHandler->getLayoutHandler();
        $this->collectionHandler = $persistenceHandler->getCollectionHandler();
    }

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlock($blockId, $locales = null)
    {
        $this->validator->validateId($blockId, 'blockId');

        $block = $this->blockHandler->loadBlock($blockId, Value::STATUS_PUBLISHED);

        if (empty($block->parentId)) {
            // We do not allow loading root zone blocks
            throw new NotFoundException('block', $blockId);
        }

        return $this->mapper->mapBlock($block, $locales);
    }

    /**
     * Loads a block draft with specified ID.
     *
     * @param int|string $blockId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function loadBlockDraft($blockId, $locales = null)
    {
        $this->validator->validateId($blockId, 'blockId');

        $block = $this->blockHandler->loadBlock($blockId, Value::STATUS_DRAFT);

        if (empty($block->parentId)) {
            // We do not allow loading root zone blocks
            throw new NotFoundException('block', $blockId);
        }

        return $this->mapper->mapBlock($block, $locales);
    }

    /**
     * Loads all blocks belonging to provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param string[]|bool $locales
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function loadZoneBlocks(Zone $zone, $locales = null)
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
            try {
                $blocks[] = $this->mapper->mapBlock($persistenceBlock, $locales);
            } catch (NotFoundException $e) {
                // Block does not have the translation, skip it
            }
        }

        return $blocks;
    }

    /**
     * Loads all blocks belonging to provided layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string[]|bool $locales
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function loadLayoutBlocks(Layout $layout, $locales = null)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout(
            $layout->getId(),
            $layout->getStatus()
        );

        // We filter out all root blocks, since we do not allow loading those
        $persistenceBlocks = array_filter(
            $this->blockHandler->loadLayoutBlocks($persistenceLayout),
            function (PersistenceBlock $persistenceBlock) {
                return !empty($persistenceBlock->parentId);
            }
        );

        $blocks = array();
        foreach ($persistenceBlocks as $persistenceBlock) {
            try {
                $blocks[] = $this->mapper->mapBlock($persistenceBlock, $locales);
            } catch (NotFoundException $e) {
                // Block does not have the translation, skip it
            }
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

        if (!in_array($placeholder, $targetBlockDefinition->getPlaceholders(), true)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($blockCreateStruct->definition instanceof ContainerDefinitionInterface) {
            throw new BadStateException('blockCreateStruct', 'Containers cannot be placed inside containers.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($targetBlock->getId(), Value::STATUS_DRAFT);

        return $this->internalCreateBlock($blockCreateStruct, $persistenceBlock, $placeholder, $position);
    }

    /**
     * Creates a block in specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
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

        $layout = $this->layoutService->loadLayoutDraft($zone->getLayoutId());

        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        $this->validator->validatePosition($position, 'position');
        $this->validator->validateBlockCreateStruct($blockCreateStruct);

        if (
            !$layout->getLayoutType()->isBlockAllowedInZone(
                $blockCreateStruct->definition,
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
     *                                                          If block does not have a specified translation
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

        if (!in_array($blockUpdateStruct->locale, $persistenceBlock->availableLocales, true)) {
            throw new BadStateException('block', 'Block does not have the specified translation.');
        }

        $updatedBlock = $this->transaction(
            function () use ($block, $persistenceBlock, $blockUpdateStruct) {
                $persistenceBlock = $this->updateBlockTranslations(
                    $block,
                    $persistenceBlock,
                    $blockUpdateStruct
                );

                return $this->blockHandler->updateBlock(
                    $persistenceBlock,
                    new BlockUpdateStruct(
                        array(
                            'viewType' => $blockUpdateStruct->viewType,
                            'itemViewType' => $blockUpdateStruct->itemViewType,
                            'name' => $blockUpdateStruct->name,
                            'alwaysAvailable' => $blockUpdateStruct->alwaysAvailable,
                            'config' => $this->configMapper->serializeValues(
                                $blockUpdateStruct->getConfigStructs(),
                                $block->getDefinition()->getConfigDefinitions(),
                                $persistenceBlock->config
                            ),
                        )
                    )
                );
            }
        );

        return $this->mapper->mapBlock($updatedBlock, $block->getAvailableLocales());
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
     *                                                          If target block is in a different layout
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

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceTargetBlock = $this->blockHandler->loadBlock($targetBlock->getId(), Value::STATUS_DRAFT);

        if ($persistenceBlock->layoutId !== $persistenceTargetBlock->layoutId) {
            throw new BadStateException('targetBlock', 'You can only copy block to blocks in the same layout.');
        }

        $this->validator->validateIdentifier($placeholder, 'placeholder', true);

        $targetBlockDefinition = $targetBlock->getDefinition();

        if (!$targetBlockDefinition instanceof ContainerDefinitionInterface) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!in_array($placeholder, $targetBlockDefinition->getPlaceholders(), true)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($block->getDefinition() instanceof ContainerDefinitionInterface) {
            throw new BadStateException('block', 'Containers cannot be placed inside containers.');
        }

        $copiedBlock = $this->transaction(
            function () use ($persistenceBlock, $persistenceTargetBlock, $placeholder) {
                return $this->blockHandler->copyBlock($persistenceBlock, $persistenceTargetBlock, $placeholder);
            }
        );

        return $this->mapper->mapBlock($copiedBlock, $block->getAvailableLocales());
    }

    /**
     * Copies a block to a specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block or zone are not drafts
     *                                                          If zone is in a different layout
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
            throw new BadStateException('zone', 'You can only copy blocks in draft zones.');
        }

        $layout = $this->layoutService->loadLayoutDraft($zone->getLayoutId());

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        if ($persistenceBlock->layoutId !== $persistenceZone->layoutId) {
            throw new BadStateException('zone', 'You can only copy block to zone in the same layout.');
        }

        if (!$layout->getLayoutType()->isBlockAllowedInZone($block->getDefinition(), $persistenceZone->identifier)) {
            throw new BadStateException('zone', 'Block is not allowed in specified zone.');
        }

        $rootBlock = $this->blockHandler->loadBlock($persistenceZone->rootBlockId, $persistenceZone->status);

        $copiedBlock = $this->transaction(
            function () use ($persistenceBlock, $rootBlock) {
                return $this->blockHandler->copyBlock($persistenceBlock, $rootBlock, 'root');
            }
        );

        return $this->mapper->mapBlock($copiedBlock, $block->getAvailableLocales());
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
     *                                                          If target block is in a different layout
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

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceTargetBlock = $this->blockHandler->loadBlock($targetBlock->getId(), Value::STATUS_DRAFT);

        if ($persistenceBlock->layoutId !== $persistenceTargetBlock->layoutId) {
            throw new BadStateException('targetBlock', 'You can only move block to blocks in the same layout.');
        }

        $this->validator->validateIdentifier($placeholder, 'placeholder', true);
        $this->validator->validatePosition($position, 'position', true);

        $targetBlockDefinition = $targetBlock->getDefinition();

        if (!$targetBlockDefinition instanceof ContainerDefinitionInterface) {
            throw new BadStateException('targetBlock', 'Target block is not a container.');
        }

        if (!in_array($placeholder, $targetBlockDefinition->getPlaceholders(), true)) {
            throw new BadStateException('placeholder', 'Target block does not have the specified placeholder.');
        }

        if ($block->getDefinition() instanceof ContainerDefinitionInterface) {
            throw new BadStateException('block', 'Containers cannot be placed inside containers.');
        }

        $movedBlock = $this->internalMoveBlock($persistenceBlock, $persistenceTargetBlock, $placeholder, $position);

        return $this->mapper->mapBlock($movedBlock, $block->getAvailableLocales());
    }

    /**
     * Moves a block to specified position inside the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
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
            throw new BadStateException('zone', 'You can only move blocks in draft zones.');
        }

        $this->validator->validatePosition($position, 'position', true);

        $layout = $this->layoutService->loadLayoutDraft($zone->getLayoutId());

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        if ($persistenceBlock->layoutId !== $persistenceZone->layoutId) {
            throw new BadStateException('zone', 'You can only move block to zone in the same layout.');
        }

        if (!$layout->getLayoutType()->isBlockAllowedInZone($block->getDefinition(), $persistenceZone->identifier)) {
            throw new BadStateException('zone', 'Block is not allowed in specified zone.');
        }

        $rootBlock = $this->blockHandler->loadBlock($persistenceZone->rootBlockId, $persistenceZone->status);

        $movedBlock = $this->internalMoveBlock($persistenceBlock, $rootBlock, 'root', $position);

        return $this->mapper->mapBlock($movedBlock, $block->getAvailableLocales());
    }

    /**
     * Restores the specified block from the published status. Position of the block is kept as is.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function restoreBlock(Block $block)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'Only draft blocks can be restored.');
        }

        $draftBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);
        $draftLayout = $this->layoutHandler->loadLayout($draftBlock->layoutId, Value::STATUS_DRAFT);

        $draftBlock = $this->transaction(
            function () use ($draftBlock, $draftLayout) {
                $draftBlock = $this->blockHandler->restoreBlock($draftBlock, Value::STATUS_PUBLISHED);

                foreach ($draftLayout->availableLocales as $locale) {
                    if (!in_array($locale, $draftBlock->availableLocales, true)) {
                        $draftBlock = $this->blockHandler->createBlockTranslation(
                            $draftBlock,
                            $locale,
                            $draftBlock->mainLocale
                        );
                    }
                }

                $draftBlock = $this->blockHandler->setMainTranslation($draftBlock, $draftLayout->mainLocale);

                foreach ($draftBlock->availableLocales as $blockLocale) {
                    if (!in_array($blockLocale, $draftLayout->availableLocales, true)) {
                        $draftBlock = $this->blockHandler->deleteBlockTranslation($draftBlock, $blockLocale);
                    }
                }

                return $draftBlock;
            }
        );

        return $this->mapper->mapBlock($draftBlock, array($draftBlock->mainLocale));
    }

    /**
     * Enables translating the block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If block is already translatable
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function enableTranslations(Block $block)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'You can only enable translations for draft blocks.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);

        if ($persistenceBlock->isTranslatable) {
            throw new BadStateException('block', 'Block is already translatable.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($persistenceBlock->layoutId, Value::STATUS_DRAFT);

        $updatedBlock = $this->transaction(
            function () use ($persistenceBlock, $persistenceLayout) {
                $updatedBlock = $this->blockHandler->updateBlock(
                    $persistenceBlock,
                    new BlockUpdateStruct(
                        array(
                            'isTranslatable' => true,
                        )
                    )
                );

                foreach ($persistenceLayout->availableLocales as $locale) {
                    if (!in_array($locale, $updatedBlock->availableLocales, true)) {
                        $updatedBlock = $this->blockHandler->createBlockTranslation(
                            $updatedBlock,
                            $locale,
                            $updatedBlock->mainLocale
                        );
                    }
                }

                return $updatedBlock;
            }
        );

        return $this->mapper->mapBlock($updatedBlock, array($updatedBlock->mainLocale));
    }

    /**
     * Disable translating the block. All translations (except the main one) will be removed.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is not a draft
     *                                                          If block is not translatable
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function disableTranslations(Block $block)
    {
        if ($block->isPublished()) {
            throw new BadStateException('block', 'You can only disable translations for draft blocks.');
        }

        $persistenceBlock = $this->blockHandler->loadBlock($block->getId(), Value::STATUS_DRAFT);

        if (!$persistenceBlock->isTranslatable) {
            throw new BadStateException('block', 'Block is not translatable.');
        }

        $updatedBlock = $this->transaction(
            function () use ($persistenceBlock) {
                $persistenceBlock = $this->blockHandler->updateBlock(
                    $persistenceBlock,
                    new BlockUpdateStruct(
                        array(
                            'isTranslatable' => false,
                        )
                    )
                );

                foreach ($persistenceBlock->availableLocales as $locale) {
                    if ($locale !== $persistenceBlock->mainLocale) {
                        $persistenceBlock = $this->blockHandler->deleteBlockTranslation(
                            $persistenceBlock,
                            $locale
                        );
                    }
                }

                return $persistenceBlock;
            }
        );

        return $this->mapper->mapBlock($updatedBlock, array($updatedBlock->mainLocale));
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

        $this->transaction(
            function () use ($persistenceBlock) {
                $this->blockHandler->deleteBlock($persistenceBlock);
            }
        );
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
     * @param string $locale
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct
     */
    public function newBlockUpdateStruct($locale, Block $block = null)
    {
        return $this->structBuilder->newBlockUpdateStruct($locale, $block);
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
        $persistenceLayout = $this->layoutHandler->loadLayout($targetBlock->layoutId, Value::STATUS_DRAFT);

        $createdBlock = $this->transaction(
            function () use ($blockCreateStruct, $persistenceLayout, $targetBlock, $placeholder, $position) {
                $createdBlock = $this->blockHandler->createBlock(
                    new BlockCreateStruct(
                        array(
                            'status' => $targetBlock->status,
                            'position' => $position,
                            'definitionIdentifier' => $blockCreateStruct->definition->getIdentifier(),
                            'viewType' => $blockCreateStruct->viewType,
                            'itemViewType' => $blockCreateStruct->itemViewType,
                            'name' => $blockCreateStruct->name,
                            'alwaysAvailable' => $blockCreateStruct->alwaysAvailable,
                            'isTranslatable' => $blockCreateStruct->isTranslatable,
                            'parameters' => $this->parameterMapper->serializeValues(
                                $blockCreateStruct->definition,
                                $blockCreateStruct->getParameterValues()
                            ),
                            'config' => $this->configMapper->serializeValues(
                                $blockCreateStruct->getConfigStructs(),
                                $blockCreateStruct->definition->getConfigDefinitions()
                            ),
                        )
                    ),
                    $persistenceLayout,
                    $targetBlock,
                    $placeholder
                );

                $blockConfig = $blockCreateStruct->definition->getConfig();
                foreach ($blockConfig->getCollections() as $collectionConfig) {
                    $createdCollection = $this->collectionHandler->createCollection(
                        new CollectionCreateStruct(
                            array(
                                'status' => Value::STATUS_DRAFT,
                                'alwaysAvailable' => $blockCreateStruct->alwaysAvailable,
                                'isTranslatable' => $blockCreateStruct->isTranslatable,
                                'mainLocale' => $persistenceLayout->mainLocale,
                            )
                        )
                    );

                    if ($blockCreateStruct->isTranslatable) {
                        foreach ($persistenceLayout->availableLocales as $locale) {
                            if ($locale !== $persistenceLayout->mainLocale) {
                                $createdCollection = $this->collectionHandler->createCollectionTranslation(
                                    $createdCollection,
                                    $locale,
                                    $createdCollection->mainLocale
                                );
                            }
                        }
                    }

                    $this->blockHandler->createCollectionReference(
                        $createdBlock,
                        new CollectionReferenceCreateStruct(
                            array(
                                'identifier' => $collectionConfig->getIdentifier(),
                                'collection' => $createdCollection,
                                'offset' => 0,
                                'limit' => null,
                            )
                        )
                    );
                }

                return $createdBlock;
            }
        );

        return $this->mapper->mapBlock($createdBlock, array($createdBlock->mainLocale));
    }

    /**
     * Moves a block. Internal method unifying moving a block to a zone and to a parent block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    protected function internalMoveBlock(PersistenceBlock $block, PersistenceBlock $targetBlock, $placeholder, $position)
    {
        return $this->transaction(
            function () use ($block, $targetBlock, $placeholder, $position) {
                if ($block->parentId === $targetBlock->id && $block->placeholder === $placeholder) {
                    return $this->blockHandler->moveBlockToPosition($block, $position);
                }

                return $this->blockHandler->moveBlock(
                    $block,
                    $targetBlock,
                    $placeholder,
                    $position
                );
            }
        );
    }

    /**
     * Updates translations for specified blocks.
     *
     * This makes sure that untranslatable parameters are always kept in sync between all
     * available translations in the block. This means that if main translation is updated,
     * all other translations need to be updated too to reflect changes to untranslatable params,
     * and if any other translation is updated, it needs to take values of untranslatable params
     * from the main translation.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $persistenceBlock
     * @param \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    protected function updateBlockTranslations(Block $block, PersistenceBlock $persistenceBlock, APIBlockUpdateStruct $blockUpdateStruct)
    {
        if ($blockUpdateStruct->locale === $persistenceBlock->mainLocale) {
            $persistenceBlock = $this->blockHandler->updateBlockTranslation(
                $persistenceBlock,
                $blockUpdateStruct->locale,
                new TranslationUpdateStruct(
                    array(
                        'parameters' => $this->parameterMapper->serializeValues(
                            $block->getDefinition(),
                            $blockUpdateStruct->getParameterValues(),
                            $persistenceBlock->parameters[$persistenceBlock->mainLocale]
                        ),
                    )
                )
            );
        }

        $untranslatableParams = $this->parameterMapper->extractUntranslatableParameters(
            $block->getDefinition(),
            $persistenceBlock->parameters[$persistenceBlock->mainLocale]
        );

        $localesToUpdate = array($blockUpdateStruct->locale);
        if ($persistenceBlock->mainLocale === $blockUpdateStruct->locale) {
            $localesToUpdate = $persistenceBlock->availableLocales;

            // Remove the main locale from the array, since we already updated that one
            array_splice($localesToUpdate, array_search($persistenceBlock->mainLocale, $persistenceBlock->availableLocales, true), 1);
        }

        foreach ($localesToUpdate as $locale) {
            $params = $persistenceBlock->parameters[$locale];

            if ($locale === $blockUpdateStruct->locale) {
                $params = $this->parameterMapper->serializeValues(
                    $block->getDefinition(),
                    $blockUpdateStruct->getParameterValues(),
                    $params
                );
            }

            $persistenceBlock = $this->blockHandler->updateBlockTranslation(
                $persistenceBlock,
                $locale,
                new TranslationUpdateStruct(
                    array(
                        'parameters' => $untranslatableParams + $params,
                    )
                )
            );
        }

        return $persistenceBlock;
    }
}
