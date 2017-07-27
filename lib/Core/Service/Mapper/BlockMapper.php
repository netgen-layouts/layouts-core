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
use Netgen\BlockManager\Locale\LocaleContextInterface;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Block\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference as PersistenceCollectionReference;

class BlockMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    protected $configMapper;

    /**
     * @var \Netgen\BlockManager\Locale\LocaleContextInterface
     */
    protected $localeContext;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper $collectionMapper
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper $configMapper
     * @param \Netgen\BlockManager\Locale\LocaleContextInterface $localeContext
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(
        Handler $persistenceHandler,
        CollectionMapper $collectionMapper,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        LocaleContextInterface $localeContext,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->collectionMapper = $collectionMapper;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->localeContext = $localeContext;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
    }

    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string[] $locales
     * @param bool $useContext
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the block does not have any currently available translations
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function mapBlock(PersistenceBlock $block, array $locales = null, $useContext = true)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $block->definitionIdentifier
        );

        $translations = array();
        $blockLocales = $locales !== null ? $locales : $this->getBlockLocales($block, $useContext);

        foreach ($blockLocales as $locale) {
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
     * Builds the API collection reference value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReference $collectionReference
     * @param array $locales
     * @param bool $useContext
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference
     */
    public function mapCollectionReference(
        PersistenceBlock $block,
        PersistenceCollectionReference $collectionReference,
        array $locales = null,
        $useContext = true
    ) {
        $collection = $this->persistenceHandler->getCollectionHandler()->loadCollection(
            $collectionReference->collectionId,
            $collectionReference->collectionStatus
        );

        return new CollectionReference(
            array(
                'block' => $this->mapBlock($block, $locales, $useContext),
                'collection' => $this->collectionMapper->mapCollection($collection, $locales, $useContext),
                'identifier' => $collectionReference->identifier,
                'offset' => $collectionReference->offset,
                'limit' => $collectionReference->limit,
            )
        );
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
    protected function mapBlockTranslation(PersistenceBlock $block, BlockDefinitionInterface $definition, $locale)
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
    protected function mapPlaceholders(PersistenceBlock $block, BlockDefinitionInterface $blockDefinition)
    {
        if (!$blockDefinition instanceof ContainerDefinitionInterface) {
            return array();
        }

        $childBlocks = $this->persistenceHandler->getBlockHandler()->loadChildBlocks($block);

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

    /**
     * Returns the valid locales for the provided block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param bool $useContext
     *
     * @return string[]
     */
    protected function getBlockLocales(PersistenceBlock $block, $useContext = true)
    {
        $locales = $useContext ? $this->localeContext->getLocaleCodes() : $block->availableLocales;
        if ($block->alwaysAvailable && !in_array($block->mainLocale, $locales, true)) {
            $locales[] = $block->mainLocale;
        }

        return $locales;
    }
}
