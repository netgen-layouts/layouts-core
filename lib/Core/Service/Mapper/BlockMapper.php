<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\LazyCollection;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;

final class BlockMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    private $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    private $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    private $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    private $configMapper;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    private $blockDefinitionRegistry;

    public function __construct(
        BlockHandlerInterface $blockHandler,
        CollectionHandlerInterface $collectionHandler,
        CollectionMapper $collectionMapper,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    ) {
        $this->blockHandler = $blockHandler;
        $this->collectionHandler = $collectionHandler;
        $this->collectionMapper = $collectionMapper;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
    }

    /**
     * Builds the API block value from persistence one.
     *
     * If not empty, the first available locale in $locales array will be returned.
     *
     * If the block is always available and $useMainLocale is set to true,
     * block in main locale will be returned if none of the locales in $locales
     * array are found.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param array $locales
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the block does not have any requested translations
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function mapBlock(PersistenceBlock $block, array $locales = null, $useMainLocale = true)
    {
        try {
            $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
                $block->definitionIdentifier
            );
        } catch (BlockDefinitionException $e) {
            $blockDefinition = new NullBlockDefinition($block->definitionIdentifier);
        }

        $locales = !empty($locales) ? $locales : [$block->mainLocale];
        if ($useMainLocale && $block->alwaysAvailable) {
            $locales[] = $block->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $block->availableLocales));
        if (empty($validLocales)) {
            throw new NotFoundException('block', $block->id);
        }

        $blockLocale = reset($validLocales);
        $untranslatableParams = $this->parameterMapper->extractUntranslatableParameters(
            $blockDefinition,
            $block->parameters[$block->mainLocale]
        );

        $blockData = [
            'id' => $block->id,
            'layoutId' => $block->layoutId,
            'definition' => $blockDefinition,
            'viewType' => $block->viewType,
            'itemViewType' => $block->itemViewType,
            'name' => $block->name,
            'parentPosition' => $block->position,
            'status' => $block->status,
            'placeholders' => $this->mapPlaceholders($block, $blockDefinition, $locales),
            'collectionReferences' => $this->mapCollectionReferences($block, $locales),
            'configs' => $this->configMapper->mapConfig($block->config, $blockDefinition->getConfigDefinitions()),
            'isTranslatable' => $block->isTranslatable,
            'mainLocale' => $block->mainLocale,
            'alwaysAvailable' => $block->alwaysAvailable,
            'availableLocales' => $block->availableLocales,
            'locale' => $blockLocale,
            'parameters' => $this->parameterMapper->mapParameters(
                $blockDefinition,
                $untranslatableParams + $block->parameters[$blockLocale]
            ),
        ];

        return new Block($blockData);
    }

    /**
     * Builds the API collection reference values for the provided block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param array $locales
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    private function mapCollectionReferences(PersistenceBlock $block, array $locales = null)
    {
        $collectionReferences = $this->blockHandler->loadCollectionReferences($block);

        $mappedReferences = [];
        foreach ($collectionReferences as $collectionReference) {
            $mappedReferences[$collectionReference->identifier] = new CollectionReference(
                [
                    'collection' => function () use ($collectionReference, $locales) {
                        $collection = $this->collectionHandler->loadCollection(
                            $collectionReference->collectionId,
                            $collectionReference->collectionStatus
                        );

                        return $this->collectionMapper->mapCollection($collection, $locales, false);
                    },
                    'identifier' => $collectionReference->identifier,
                ]
            );
        }

        return $mappedReferences;
    }

    /**
     * Maps the placeholder from persistence parameters.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     * @param array $locales
     *
     * @return \Netgen\BlockManager\Core\Values\Block\Placeholder[]
     */
    private function mapPlaceholders(PersistenceBlock $block, BlockDefinitionInterface $blockDefinition, array $locales = null)
    {
        if (!$blockDefinition instanceof ContainerDefinitionInterface) {
            return [];
        }

        $placeholders = [];
        foreach ($blockDefinition->getPlaceholders() as $placeholderIdentifier) {
            $placeholders[$placeholderIdentifier] = new Placeholder(
                [
                    'identifier' => $placeholderIdentifier,
                    'blocks' => new LazyCollection(
                        function () use ($block, $placeholderIdentifier, $locales) {
                            return array_map(
                                function (PersistenceBlock $childBlock) use ($locales) {
                                    return $this->mapBlock($childBlock, $locales, false);
                                },
                                $this->blockHandler->loadChildBlocks($block, $placeholderIdentifier)
                            );
                        }
                    ),
                ]
            );
        }

        return $placeholders;
    }
}
