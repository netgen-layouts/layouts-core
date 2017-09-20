<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\BlockTranslation;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;

class BlockMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    private $persistenceHandler;

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

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    private $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    private $collectionHandler;

    public function __construct(
        Handler $persistenceHandler,
        CollectionMapper $collectionMapper,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->collectionMapper = $collectionMapper;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;

        $this->blockHandler = $this->persistenceHandler->getBlockHandler();
        $this->collectionHandler = $this->persistenceHandler->getCollectionHandler();
    }

    /**
     * Builds the API block value object from persistence one.
     *
     * If $locales is an array, returned block will only have specified translations.
     * If $locales is true, returned block will have all translations, otherwise, the main
     * translation will be returned.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the block does not have any currently available translations
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function mapBlock(PersistenceBlock $block, $locales = null)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $block->definitionIdentifier
        );

        if ($locales === true) {
            $locales = $block->availableLocales;
            sort($locales);
        } elseif (!is_array($locales) || empty($locales)) {
            $locales = array($block->mainLocale);
        }

        if ($block->alwaysAvailable && !in_array($block->mainLocale, $locales, true)) {
            $locales[] = $block->mainLocale;
        }

        $translations = array();
        foreach ($locales as $locale) {
            if (in_array($locale, $block->availableLocales, true)) {
                $translations[$locale] = $this->mapBlockTranslation($block, $blockDefinition, $locale);
            }
        }

        if (empty($translations)) {
            throw new NotFoundException('block', $block->id);
        }

        $blockData = array(
            'id' => $block->id,
            'layoutId' => $block->layoutId,
            'definition' => $blockDefinition,
            'viewType' => $block->viewType,
            'itemViewType' => $block->itemViewType,
            'name' => $block->name,
            'status' => $block->status,
            'published' => $block->status === Value::STATUS_PUBLISHED,
            'placeholders' => $this->mapPlaceholders($block, $blockDefinition),
            'collectionReferences' => $this->mapCollectionReferences($block, $locales),
            'configs' => $this->configMapper->mapConfig($block->config, $blockDefinition->getConfigDefinitions()),
            'isTranslatable' => $block->isTranslatable,
            'mainLocale' => $block->mainLocale,
            'alwaysAvailable' => $block->alwaysAvailable,
            'availableLocales' => array_keys($translations),
            'translations' => $translations,
        );

        return new Block($blockData);
    }

    /**
     * Builds the API collection reference value objects for the provided block.
     *
     * If $locales is an array, returned references will only have specified translations.
     * If $locales is true, returned references will have all translations, otherwise, the main
     * translation will be returned.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string[]|bool $locales
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    private function mapCollectionReferences(PersistenceBlock $block, $locales = null)
    {
        $collectionReferences = $this->blockHandler->loadCollectionReferences($block);

        $mappedReferences = array();
        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $collectionReference->collectionStatus
            );

            $mappedReferences[$collectionReference->identifier] = new CollectionReference(
                array(
                    'collection' => $this->collectionMapper->mapCollection($collection, $locales),
                    'identifier' => $collectionReference->identifier,
                    'offset' => $collectionReference->offset,
                    'limit' => $collectionReference->limit,
                )
            );
        }

        return $mappedReferences;
    }

    /**
     * Maps the block translation for the provided locale.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $definition
     * @param string $locale
     *
     * @return \Netgen\BlockManager\Core\Values\Block\BlockTranslation
     */
    private function mapBlockTranslation(PersistenceBlock $block, BlockDefinitionInterface $definition, $locale)
    {
        $untranslatableParams = $this->parameterMapper->extractUntranslatableParameters(
            $definition,
            $block->parameters[$block->mainLocale]
        );

        return new BlockTranslation(
            array(
                'locale' => $locale,
                'isMainTranslation' => $locale === $block->mainLocale,
                'parameters' => $this->parameterMapper->mapParameters(
                    $definition,
                    $untranslatableParams + $block->parameters[$locale]
                ),
            )
        );
    }

    /**
     * Maps the placeholder from persistence parameters.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     *
     * @return \Netgen\BlockManager\Core\Values\Block\Placeholder[]
     */
    private function mapPlaceholders(PersistenceBlock $block, BlockDefinitionInterface $blockDefinition)
    {
        if (!$blockDefinition instanceof ContainerDefinitionInterface) {
            return array();
        }

        $childBlocks = $this->blockHandler->loadChildBlocks($block);

        $placeholders = array();
        foreach ($blockDefinition->getPlaceholders() as $placeholderIdentifier) {
            $placeholderBlocks = array();
            foreach ($childBlocks as $childBlock) {
                if ($childBlock->placeholder === $placeholderIdentifier) {
                    $placeholderBlocks[] = $this->mapBlock($childBlock);
                }
            }

            $placeholders[$placeholderIdentifier] = new Placeholder(
                array(
                    'identifier' => $placeholderIdentifier,
                    'blocks' => $placeholderBlocks,
                )
            );
        }

        return $placeholders;
    }
}
