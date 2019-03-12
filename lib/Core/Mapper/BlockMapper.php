<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Mapper;

use Generator;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\LazyCollection;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;

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
     * @var \Netgen\BlockManager\Core\Mapper\CollectionMapper
     */
    private $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Core\Mapper\ParameterMapper
     */
    private $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Core\Mapper\ConfigMapper
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
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the block does not have any requested translations
     */
    public function mapBlock(PersistenceBlock $block, ?array $locales = null, bool $useMainLocale = true): Block
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

        $blockLocale = array_values($validLocales)[0];
        $untranslatableParams = iterator_to_array(
            $this->parameterMapper->extractUntranslatableParameters(
                $blockDefinition,
                $block->parameters[$block->mainLocale]
            )
        );

        $blockData = [
            'id' => $block->id,
            'layoutId' => $block->layoutId,
            'definition' => $blockDefinition,
            'viewType' => $block->viewType,
            'itemViewType' => $block->itemViewType,
            'name' => $block->name,
            'position' => $block->position,
            'parentBlockId' => $block->parentId,
            'parentPlaceholder' => $block->placeholder,
            'status' => $block->status,
            'placeholders' => iterator_to_array($this->mapPlaceholders($block, $blockDefinition, $locales)),
            'collections' => new LazyCollection(
                function () use ($block, $locales): array {
                    return array_map(
                        function (PersistenceCollection $collection) use ($locales): Collection {
                            return $this->collectionMapper->mapCollection($collection, $locales);
                        },
                        iterator_to_array($this->loadCollections($block))
                    );
                }
            ),
            'configs' => iterator_to_array(
                $this->configMapper->mapConfig(
                    $block->config,
                    $blockDefinition->getConfigDefinitions()
                )
            ),
            'isTranslatable' => $block->isTranslatable,
            'mainLocale' => $block->mainLocale,
            'alwaysAvailable' => $block->alwaysAvailable,
            'availableLocales' => $block->availableLocales,
            'locale' => $blockLocale,
            'parameters' => iterator_to_array(
                $this->parameterMapper->mapParameters(
                    $blockDefinition,
                    $untranslatableParams + $block->parameters[$blockLocale]
                )
            ),
        ];

        return Block::fromArray($blockData);
    }

    /**
     * Loads all persistence collections belonging to the provided block.
     */
    private function loadCollections(PersistenceBlock $block): Generator
    {
        $collectionReferences = $this->blockHandler->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            yield $collectionReference->identifier => $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );
        }
    }

    /**
     * Maps the placeholder from persistence parameters.
     */
    private function mapPlaceholders(PersistenceBlock $block, BlockDefinitionInterface $blockDefinition, ?array $locales = null): Generator
    {
        if (!$blockDefinition instanceof ContainerDefinitionInterface) {
            return;
        }

        foreach ($blockDefinition->getPlaceholders() as $placeholderIdentifier) {
            yield $placeholderIdentifier => Placeholder::fromArray(
                [
                    'identifier' => $placeholderIdentifier,
                    'blocks' => new LazyCollection(
                        function () use ($block, $placeholderIdentifier, $locales): array {
                            return array_map(
                                function (PersistenceBlock $childBlock) use ($locales): Block {
                                    return $this->mapBlock($childBlock, $locales, false);
                                },
                                $this->blockHandler->loadChildBlocks($block, $placeholderIdentifier)
                            );
                        }
                    ),
                ]
            );
        }
    }
}
