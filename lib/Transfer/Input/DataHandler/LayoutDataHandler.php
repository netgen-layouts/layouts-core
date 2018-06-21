<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Input\DataHandler;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Config\ConfigDefinitionAwareInterface;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;

/**
 * LayoutDataHandler handles serialized Layout data.
 */
final class LayoutDataHandler
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    private $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface
     */
    private $itemDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Item\CmsItemLoaderInterface
     */
    private $cmsItemLoader;

    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        LayoutService $layoutService,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        ItemDefinitionRegistryInterface $itemDefinitionRegistry,
        QueryTypeRegistryInterface $queryTypeRegistry,
        CmsItemLoaderInterface $cmsItemLoader
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->layoutService = $layoutService;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->itemDefinitionRegistry = $itemDefinitionRegistry;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->cmsItemLoader = $cmsItemLoader;
    }

    /**
     * Create and return layout from the given serialized $data.
     */
    public function createLayout(array $data): Layout
    {
        $createStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType($data['type_identifier']),
            sprintf('%s (Imported on %s)', $data['name'], date('D, d M Y H:i:s')),
            $data['main_locale']
        );

        $createStruct->description = $data['description'];
        $createStruct->shared = $data['is_shared'];

        return $this->layoutService->transaction(
            function () use ($createStruct, $data): Layout {
                $layoutDraft = $this->layoutService->createLayout($createStruct);
                $this->addTranslations($layoutDraft, $data);
                $this->processZones($layoutDraft, $data);

                return $this->layoutService->publishLayout($layoutDraft);
            }
        );
    }

    /**
     * Processes zones in the given $layout from the $layoutData.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If data is not consistent
     */
    private function processZones(Layout $layout, array $layoutData): void
    {
        foreach ($layout->getZones() as $zone) {
            if (!array_key_exists($zone->getIdentifier(), $layoutData['zones'])) {
                throw new RuntimeException(
                    sprintf('Missing data for zone "%s"', $zone->getIdentifier())
                );
            }

            $this->processZone($zone, $layoutData['zones'][$zone->getIdentifier()]);
        }
    }

    /**
     * Add translations to the $layout from the given $layoutData.
     */
    private function addTranslations(Layout $layout, array $layoutData): void
    {
        $translationLocales = $this->extractTranslationLocales($layoutData);

        foreach ($translationLocales as $locale) {
            $this->layoutService->addTranslation($layout, $locale, $layoutData['main_locale']);
        }
    }

    /**
     * Extract translation locales from the given $layoutData.
     *
     * @return string[]
     */
    private function extractTranslationLocales(array $layoutData): array
    {
        $availableLocalesSet = array_flip($layoutData['available_locales']);
        unset($availableLocalesSet[$layoutData['main_locale']]);

        return array_keys($availableLocalesSet);
    }

    /**
     * Update all translations of the given block with the $translationsData.
     *
     * $translationsData is an array of parameters, indexed by translation locale.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If translation data is not consistent
     */
    private function updateBlockTranslations(Block $block, array $translationsData): void
    {
        $mainLocale = $block->getMainLocale();

        foreach ($block->getAvailableLocales() as $locale) {
            if ($locale === $mainLocale) {
                continue;
            }

            if (!array_key_exists($locale, $translationsData)) {
                throw new RuntimeException(
                    sprintf('Could not find locale "%s" in the given block data', $locale)
                );
            }

            $this->updateBlockTranslation($block, $translationsData[$locale], $locale);
        }
    }

    /**
     * Update given $block with $parameterData for the $locale.
     */
    private function updateBlockTranslation(Block $block, array $parameterData, string $locale): void
    {
        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);
        $updateStruct->fillParametersFromHash($block->getDefinition(), $parameterData, true);

        $this->blockService->updateBlock($block, $updateStruct);
    }

    /**
     * Update all translations of the given query with $translationsData.
     *
     * $translationsData is an array of query parameters indexed by translation locale.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If translation data is not consistent
     */
    private function updateQueryTranslations(Query $query, array $translationsData): void
    {
        $mainLocale = $query->getMainLocale();

        foreach ($query->getAvailableLocales() as $locale) {
            if ($locale === $mainLocale) {
                continue;
            }

            if (!array_key_exists($locale, $translationsData)) {
                throw new RuntimeException(
                    sprintf('Could not find locale "%s" in the given query data', $locale)
                );
            }

            $this->updateQueryTranslation($query, $translationsData[$locale], $locale);
        }
    }

    /**
     * Update given $query with $parameterData for the $locale.
     */
    private function updateQueryTranslation(Query $query, array $parameterData, string $locale): void
    {
        $updateStruct = $this->collectionService->newQueryUpdateStruct($locale, $query);
        $updateStruct->fillParametersFromHash($query->getQueryType(), $parameterData, true);

        $this->collectionService->updateQuery($query, $updateStruct);
    }

    /**
     * Creates blocks in the given $zone or links linked zone to it.
     */
    private function processZone(Zone $zone, array $zoneData): void
    {
        $this->createBlocks($zone, $zoneData['blocks']);
        if (is_array($zoneData['linked_zone'])) {
            $this->linkZone($zone, $zoneData['linked_zone']);
        }
    }

    /**
     * Link given $zone with the zone given in $zoneData.
     */
    private function linkZone(Zone $zone, array $zoneData): void
    {
        $linkedZoneLayout = $this->layoutService->loadLayout($zoneData['layout_id']);
        $linkedZone = $linkedZoneLayout->getZone($zoneData['identifier']);

        if ($linkedZone instanceof Zone) {
            $this->layoutService->linkZone($zone, $linkedZone);
        }
    }

    /**
     * Create blocks in the given $zone from the given $blocksData.
     */
    private function createBlocks(Zone $zone, array $blocksData): void
    {
        foreach ($blocksData as $blockData) {
            $this->createBlockInZone($zone, $blockData);
        }
    }

    /**
     * Create a block in the given $zone from the given $blockData.
     */
    private function createBlockInZone(Zone $zone, array $blockData): Block
    {
        $blockCreateStruct = $this->buildBlockCreateStruct($blockData);
        $block = $this->blockService->createBlockInZone($blockCreateStruct, $zone);

        $this->updateBlockTranslations($block, $blockData['parameters']);
        $this->processPlaceholderBlocks($block, $blockData['placeholders']);
        $this->processCollections($block, $blockData['collections']);

        return $block;
    }

    /**
     * Create a block in the given $targetBlock and $placeholder from the given $blockData.
     */
    private function createBlock(Block $targetBlock, string $placeholder, array $blockData): Block
    {
        $blockCreateStruct = $this->buildBlockCreateStruct($blockData);
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, $placeholder);

        $this->updateBlockTranslations($block, $blockData['parameters']);
        $this->processPlaceholderBlocks($block, $blockData['placeholders']);
        $this->processCollections($block, $blockData['collections']);

        return $block;
    }

    /**
     * Creates sub-blocks in $targetBlock from provided placeholder $data.
     */
    private function processPlaceholderBlocks(Block $targetBlock, array $data): void
    {
        foreach ($data as $placeholder => $placeholderData) {
            foreach ($placeholderData['blocks'] as $blockData) {
                $this->createBlock($targetBlock, $placeholder, $blockData);
            }
        }
    }

    /**
     * Builds the block create struct from provided $blockData.
     */
    private function buildBlockCreateStruct(array $blockData): BlockCreateStruct
    {
        if (!array_key_exists($blockData['main_locale'], $blockData['parameters'])) {
            throw new RuntimeException(
                sprintf('Missing data for block main locale "%s"', $blockData['main_locale'])
            );
        }

        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($blockData['definition_identifier']);

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->name = $blockData['name'];
        $blockCreateStruct->viewType = $blockData['view_type'];
        $blockCreateStruct->itemViewType = $blockData['item_view_type'];
        $blockCreateStruct->isTranslatable = $blockData['is_translatable'];
        $blockCreateStruct->alwaysAvailable = $blockData['is_always_available'];
        $blockCreateStruct->fillParametersFromHash($blockDefinition, $blockData['parameters'][$blockData['main_locale']], true);
        $this->setConfigStructs($blockCreateStruct, $blockDefinition, $blockData['configuration'] ?? []);
        $this->setCollectionStructs($blockCreateStruct, $blockData['collections']);

        return $blockCreateStruct;
    }

    /**
     * Set collection structs to the given $blockCreateStruct.
     */
    private function setCollectionStructs(BlockCreateStruct $blockCreateStruct, array $data): void
    {
        foreach ($data as $collectionIdentifier => $collectionData) {
            $queryCreateStruct = null;

            if (is_array($collectionData['query'])) {
                if (!array_key_exists($collectionData['main_locale'], $collectionData['query']['parameters'])) {
                    throw new RuntimeException(
                        sprintf('Missing data for query main locale "%s"', $collectionData['main_locale'])
                    );
                }

                $queryType = $this->queryTypeRegistry->getQueryType($collectionData['query']['query_type']);
                $queryCreateStruct = $this->collectionService->newQueryCreateStruct($queryType);

                $queryCreateStruct->fillParametersFromHash($queryType, $collectionData['query']['parameters'][$collectionData['main_locale']], true);
            }

            $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct($queryCreateStruct);
            $collectionCreateStruct->offset = $collectionData['offset'];
            $collectionCreateStruct->limit = $collectionData['limit'];

            $blockCreateStruct->addCollectionCreateStruct($collectionIdentifier, $collectionCreateStruct);
        }
    }

    /**
     * Set configuration structs to the given $blockCreateStruct.
     */
    private function setConfigStructs(
        ConfigAwareStruct $configAwareStruct,
        ConfigDefinitionAwareInterface $configDefinitionAware,
        array $configurationData
    ): void {
        $configDefinitions = $configDefinitionAware->getConfigDefinitions();

        foreach ($configurationData as $configKey => $hash) {
            $configStruct = new ConfigStruct();
            $configStruct->fillParametersFromHash($configDefinitions[$configKey], $hash, true);
            $configAwareStruct->setConfigStruct($configKey, $configStruct);
        }
    }

    /**
     * Process collections in the given $block.
     */
    private function processCollections(Block $block, array $collectionsData): void
    {
        foreach ($block->getCollections() as $identifier => $collection) {
            $collectionData = $collectionsData[$identifier];

            $collectionQuery = $collection->getQuery();

            if ($collectionQuery instanceof Query && is_array($collectionData['query'])) {
                $this->updateQueryTranslations(
                    $collectionQuery,
                    $collectionData['query']['parameters']
                );
            }

            $this->createItems($collection, $collectionData['items']);
        }
    }

    /**
     * Create items in the $collection from the given $collectionItemsData.
     */
    private function createItems(Collection $collection, array $collectionItemsData): void
    {
        foreach ($collectionItemsData as $collectionItemData) {
            $itemDefinition = $this->itemDefinitionRegistry->getItemDefinition($collectionItemData['value_type']);

            $item = $this->cmsItemLoader->loadByRemoteId(
                $collectionItemData['value'],
                $collectionItemData['value_type']
            );

            $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                $itemDefinition,
                $this->mapItemType($collectionItemData['type']),
                $item->getValue()
            );

            $this->setConfigStructs(
                $itemCreateStruct,
                $itemDefinition,
                $collectionItemData['configuration'] ?? []
            );

            $this->collectionService->addItem($collection, $itemCreateStruct, $collectionItemData['position']);
        }
    }

    /**
     * Map items' exported type string to the real type value.
     */
    private function mapItemType(string $typeString): int
    {
        if ($typeString === 'OVERRIDE') {
            return Item::TYPE_OVERRIDE;
        }

        return Item::TYPE_MANUAL;
    }
}
