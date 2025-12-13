<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Block\PlaceholderList;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use Netgen\Layouts\API\Values\Config\ConfigList;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Block\ContainerDefinitionInterface;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\Layouts\Persistence\Values\Collection\Collection as PersistenceCollection;
use Symfony\Component\Uid\Uuid;

use function array_first;
use function array_intersect;
use function array_map;
use function array_unique;
use function count;
use function is_array;

final class BlockMapper
{
    public function __construct(
        private BlockHandlerInterface $blockHandler,
        private CollectionHandlerInterface $collectionHandler,
        private CollectionMapper $collectionMapper,
        private ParameterMapper $parameterMapper,
        private ConfigMapper $configMapper,
        private BlockDefinitionRegistry $blockDefinitionRegistry,
    ) {}

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
        } catch (BlockDefinitionException) {
            $blockDefinition = new NullBlockDefinition($block->definitionIdentifier);
        }

        $locales = is_array($locales) && count($locales) > 0 ? $locales : [$block->mainLocale];
        if ($useMainLocale && $block->isAlwaysAvailable) {
            $locales[] = $block->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $block->availableLocales));
        if (count($validLocales) === 0) {
            throw new NotFoundException('block', $block->uuid);
        }

        /** @var string $blockLocale */
        $blockLocale = array_first($validLocales);
        $untranslatableParams = [
            ...$this->parameterMapper->extractUntranslatableParameters(
                $blockDefinition,
                $block->parameters[$block->mainLocale],
            ),
        ];

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
            'status' => Status::from($block->status->value),
            'placeholders' => new PlaceholderList(
                [
                    ...$this->mapPlaceholders($block, $blockDefinition, $locales),
                ],
            ),
            'collections' => CollectionList::fromCallable(
                fn (): array => array_map(
                    fn (PersistenceCollection $collection): Collection => $this->collectionMapper->mapCollection($collection, $locales),
                    $this->collectionHandler->loadCollections($block),
                ),
            ),
            'configs' => new ConfigList(
                [
                    ...$this->configMapper->mapConfig(
                        $block->config,
                        $blockDefinition->configDefinitions,
                    ),
                ],
            ),
            'isTranslatable' => $block->isTranslatable,
            'mainLocale' => $block->mainLocale,
            'isAlwaysAvailable' => $block->isAlwaysAvailable,
            'availableLocales' => $block->availableLocales,
            'locale' => $blockLocale,
            'parameters' => new ParameterList(
                [
                    ...$this->parameterMapper->mapParameters(
                        $blockDefinition,
                        [...$block->parameters[$blockLocale], ...$untranslatableParams],
                    ),
                ],
            ),
        ];

        return Block::fromArray($blockData);
    }

    /**
     * Maps the placeholder from persistence parameters.
     *
     * @param string[]|null $locales
     *
     * @return iterable<string, \Netgen\Layouts\API\Values\Block\Placeholder>
     */
    private function mapPlaceholders(PersistenceBlock $block, BlockDefinitionInterface $blockDefinition, ?array $locales = null): iterable
    {
        if (!$blockDefinition instanceof ContainerDefinitionInterface) {
            return;
        }

        foreach ($blockDefinition->placeholders as $placeholderIdentifier) {
            yield $placeholderIdentifier => Placeholder::fromArray(
                [
                    'identifier' => $placeholderIdentifier,
                    'blocks' => BlockList::fromCallable(
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
