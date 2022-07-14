<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Generator;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\LazyCollection;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Block\ContainerDefinitionInterface;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\Layouts\Persistence\Values\Collection\Collection as PersistenceCollection;
use Ramsey\Uuid\Uuid;

use function array_intersect;
use function array_map;
use function array_unique;
use function array_values;
use function count;
use function is_array;
use function iterator_to_array;

final class BlockMapper
{
    private BlockHandlerInterface $blockHandler;

    private CollectionHandlerInterface $collectionHandler;

    private CollectionMapper $collectionMapper;

    private ParameterMapper $parameterMapper;

    private ConfigMapper $configMapper;

    private BlockDefinitionRegistry $blockDefinitionRegistry;

    public function __construct(
        BlockHandlerInterface $blockHandler,
        CollectionHandlerInterface $collectionHandler,
        CollectionMapper $collectionMapper,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        BlockDefinitionRegistry $blockDefinitionRegistry
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
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If the block does not have any requested translations
     */
    public function mapBlock(PersistenceBlock $block, ?array $locales = null, bool $useMainLocale = true): Block
    {
        try {
            $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
                $block->definitionIdentifier,
            );
        } catch (BlockDefinitionException $e) {
            $blockDefinition = new NullBlockDefinition($block->definitionIdentifier);
        }

        $locales = is_array($locales) && count($locales) > 0 ? $locales : [$block->mainLocale];
        if ($useMainLocale && $block->alwaysAvailable) {
            $locales[] = $block->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $block->availableLocales));
        if (count($validLocales) === 0) {
            throw new NotFoundException('block', $block->uuid);
        }

        /** @var string $blockLocale */
        $blockLocale = array_values($validLocales)[0];
        $untranslatableParams = iterator_to_array(
            $this->parameterMapper->extractUntranslatableParameters(
                $blockDefinition,
                $block->parameters[$block->mainLocale],
            ),
        );

        $blockData = [
            'id' => Uuid::fromString($block->uuid),
            'layoutId' => Uuid::fromString($block->layoutUuid),
            'definition' => $blockDefinition,
            'viewType' => $block->viewType,
            'itemViewType' => $block->itemViewType,
            'name' => $block->name,
            'position' => $block->position,
            'parentBlockId' => $block->depth > 1 && $block->parentUuid !== null ?
                Uuid::fromString($block->parentUuid) :
                null,
            'parentPlaceholder' => $block->depth > 1 ? $block->placeholder : null,
            'status' => $block->status,
            'placeholders' => iterator_to_array($this->mapPlaceholders($block, $blockDefinition, $locales)),
            'collections' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceCollection $collection): Collection => $this->collectionMapper->mapCollection($collection, $locales),
                    $this->collectionHandler->loadCollections($block),
                ),
            ),
            'configs' => iterator_to_array(
                $this->configMapper->mapConfig(
                    $block->config,
                    $blockDefinition->getConfigDefinitions(),
                ),
            ),
            'isTranslatable' => $block->isTranslatable,
            'mainLocale' => $block->mainLocale,
            'alwaysAvailable' => $block->alwaysAvailable,
            'availableLocales' => $block->availableLocales,
            'locale' => $blockLocale,
            'parameters' => iterator_to_array(
                $this->parameterMapper->mapParameters(
                    $blockDefinition,
                    $untranslatableParams + $block->parameters[$blockLocale],
                ),
            ),
        ];

        return Block::fromArray($blockData);
    }

    /**
     * Maps the placeholder from persistence parameters.
     *
     * @param string[]|null $locales
     *
     * @return \Generator<string, \Netgen\Layouts\API\Values\Block\Placeholder>
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
                        fn (): array => array_map(
                            fn (PersistenceBlock $childBlock): Block => $this->mapBlock($childBlock, $locales, false),
                            $this->blockHandler->loadChildBlocks($block, $placeholderIdentifier),
                        ),
                    ),
                ],
            );
        }
    }
}
