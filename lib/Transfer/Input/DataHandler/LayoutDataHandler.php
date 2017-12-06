<?php

namespace Netgen\BlockManager\Transfer\Input\DataHandler;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;

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
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry
     */
    private $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry
     */
    private $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        LayoutService $layoutService,
        BlockDefinitionRegistry $blockDefinitionRegistry,
        LayoutTypeRegistry $layoutTypeRegistry,
        QueryTypeRegistry $queryTypeRegistry,
        ItemLoaderInterface $itemLoader
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->layoutService = $layoutService;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->itemLoader = $itemLoader;
    }

    /**
     * Create and return layout from the given serialized $data.
     *
     * @param array $data
     *
     * @throws \Exception If thrown by the underlying API
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function createLayout(array $data)
    {
        $layoutType = $this->layoutTypeRegistry->getLayoutType($data['type_identifier']);
        $createStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            sprintf('%s (Imported at %s)', $data['name'], date('D, d M Y H:i:s')),
            $data['main_locale']
        );
        $createStruct->description = $data['description'];
        $createStruct->shared = $data['is_shared'];

        return $this->layoutService->transaction(
            function () use ($createStruct, $data) {
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param array $layoutData
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If data is not consistent
     * @throws \Exception If thrown by the underlying API
     */
    private function processZones(Layout $layout, array $layoutData)
    {
        foreach ($layout->getZones() as $zone) {
            if (!array_key_exists($zone->getIdentifier(), $layoutData['zones'])) {
                throw new RuntimeException(
                    sprintf("Missing data for zone '%s'", $zone->getIdentifier())
                );
            }

            $this->processZone($zone, $layoutData['zones'][$zone->getIdentifier()]);
        }
    }

    /**
     * Add translations to the $layout from the given $layoutData.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param array $layoutData
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function addTranslations(Layout $layout, array $layoutData)
    {
        $translationLocales = $this->extractTranslationLocales($layoutData);

        foreach ($translationLocales as $locale) {
            $this->layoutService->addTranslation($layout, $locale, $layoutData['main_locale']);
        }
    }

    /**
     * Extract translation locales from the given $layoutData.
     *
     * @param array $layoutData
     *
     * @return string[]
     */
    private function extractTranslationLocales(array $layoutData)
    {
        $availableLocalesSet = array_flip($layoutData['available_locales']);
        unset($availableLocalesSet[$layoutData['main_locale']]);

        return array_keys($availableLocalesSet);
    }

    /**
     * Update all translations of the given block with the $translationsData.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $translationsData Block parameters data by translation locale
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If translation data is not consistent
     * @throws \Exception If thrown by the underlying API
     */
    private function updateBlockTranslations(Block $block, array $translationsData)
    {
        $mainLocale = $block->getMainLocale();

        foreach ($block->getAvailableLocales() as $locale) {
            if ($locale === $mainLocale) {
                continue;
            }

            if (!array_key_exists($locale, $translationsData)) {
                throw new RuntimeException(
                    sprintf("Could not find locale '%s' in the given data", $locale)
                );
            }

            $this->updateBlockTranslation($block, $translationsData[$locale], $locale);
        }
    }

    /**
     * Update given $block with $parameterData for the $locale.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $parameterData
     * @param string $locale
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function updateBlockTranslation(Block $block, array $parameterData, $locale)
    {
        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);
        $updateStruct->fillParametersFromHash($block->getDefinition(), $parameterData, true);

        $this->blockService->updateBlock($block, $updateStruct);
    }

    /**
     * Update all translations of the given query with $translationsData.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param array $translationsData Query parameters data by translation locale
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If translation data is not consistent
     * @throws \Exception If thrown by the underlying API
     */
    private function updateQueryTranslations(Query $query, array $translationsData)
    {
        $mainLocale = $query->getMainLocale();

        foreach ($query->getAvailableLocales() as $locale) {
            if ($locale === $mainLocale) {
                continue;
            }

            if (!array_key_exists($locale, $translationsData)) {
                throw new RuntimeException(
                    sprintf("Could not find locale '%s' in the given data", $locale)
                );
            }

            $this->updateQueryTranslation($query, $translationsData[$locale], $locale);
        }
    }

    /**
     * Update given $query with $parameterData for the $locale.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param array $parameterData
     * @param string $locale
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function updateQueryTranslation(Query $query, array $parameterData, $locale)
    {
        $updateStruct = $this->collectionService->newQueryUpdateStruct($locale, $query);
        $updateStruct->fillParametersFromHash($query->getQueryType(), $parameterData, true);

        $this->collectionService->updateQuery($query, $updateStruct);
    }

    /**
     * Creates blocks in the given $zone or links linked zone to it.
     *
     * @see createBlocks()
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param array $zoneData
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function processZone(Zone $zone, array $zoneData)
    {
        $this->createBlocks($zone, $zoneData['blocks']);
        if (!empty($zoneData['linked_zone'])) {
            $this->linkZone($zone, $zoneData['linked_zone']);
        }
    }

    /**
     * Link given $zone with the zone given in $zoneData.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param array|null $zoneData
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function linkZone(Zone $zone, $zoneData)
    {
        $linkedZoneLayout = $this->layoutService->loadLayout($zoneData['layout_id']);
        $linkedZone = $linkedZoneLayout->getZone($zoneData['identifier']);

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * Create blocks in the given $zone from the given $blocksData.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param array $blocksData
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function createBlocks(Zone $zone, array $blocksData)
    {
        foreach ($blocksData as $blockData) {
            $this->createBlockInZone($zone, $blockData);
        }
    }

    /**
     * Create a block in the given $zone from the given $blockData.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param array $blockData
     *
     * @throws \Exception If thrown by the underlying API
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    private function createBlockInZone(Zone $zone, array $blockData)
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
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param array $blockData
     *
     * @throws \Exception If thrown by the underlying API
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    private function createBlock(Block $targetBlock, $placeholder, array $blockData)
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
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $targetBlock
     * @param array $data
     */
    private function processPlaceholderBlocks(Block $targetBlock, array $data = null)
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $placeholder => $placeholderData) {
            foreach ($placeholderData['blocks'] as $blockData) {
                $this->createBlock($targetBlock, $placeholder, $blockData);
            }
        }
    }

    /**
     * Builds the block create struct from provided $blockData.
     *
     * @param array $blockData
     *
     * @throws \Exception If thrown by the underlying API
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    private function buildBlockCreateStruct(array $blockData)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($blockData['definition_identifier']);

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->name = $blockData['name'];
        $blockCreateStruct->viewType = $blockData['view_type'];
        $blockCreateStruct->itemViewType = $blockData['item_view_type'];
        $blockCreateStruct->isTranslatable = $blockData['is_translatable'];
        $blockCreateStruct->alwaysAvailable = $blockData['is_always_available'];
        $blockCreateStruct->fillParametersFromHash($blockDefinition, $blockData['parameters'][$blockData['main_locale']], true);
        $this->setConfigStructs($blockCreateStruct, $blockDefinition, $blockData['configuration']);
        $this->setCollectionStructs($blockCreateStruct, $blockData['collections']);

        return $blockCreateStruct;
    }

    /**
     * Set collection structs to the given $blockCreateStruct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param array $data
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException
     * @throws \Exception If thrown by the underlying API
     */
    private function setCollectionStructs(BlockCreateStruct $blockCreateStruct, array $data)
    {
        foreach ($data as $collectionIdentifier => $collectionData) {
            $queryCreateStruct = null;
            if ($collectionData['query'] !== null) {
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
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     * @param array $configurationData
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException
     * @throws \Exception If thrown by the underlying API
     */
    private function setConfigStructs(
        BlockCreateStruct $blockCreateStruct,
        BlockDefinitionInterface $blockDefinition,
        array $configurationData
    ) {
        $configDefinitions = $blockDefinition->getConfigDefinitions();
        $configDefinitionMap = array();

        foreach ($configDefinitions as $configDefinition) {
            $configDefinitionMap[$configDefinition->getConfigKey()] = $configDefinition;
        }

        foreach ($configurationData as $configKey => $hash) {
            $configStruct = new ConfigStruct();
            $configStruct->fillParametersFromHash($configDefinitionMap[$configKey], $hash, true);
            $blockCreateStruct->setConfigStruct($configKey, $configStruct);
        }
    }

    /**
     * Process collections in the given $block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $collectionsData Collections data
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function processCollections(Block $block, array $collectionsData)
    {
        if (empty($collectionsData)) {
            return;
        }

        foreach ($block->getCollectionReferences() as $collectionReference) {
            $collection = $collectionReference->getCollection();
            $collectionData = $collectionsData[$collectionReference->getIdentifier()];

            if ($collection->hasQuery()) {
                $this->updateQueryTranslations($collection->getQuery(), $collectionData['query']['parameters']);
            }

            $this->createItems($collection, $collectionData['manual_items']);
            $this->createItems($collection, $collectionData['override_items']);
        }
    }

    /**
     * Create items in the $collection from the given $collectionItemsData.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param array $collectionItemsData
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException
     * @throws \Netgen\BlockManager\Exception\RuntimeException
     */
    private function createItems(Collection $collection, array $collectionItemsData)
    {
        foreach ($collectionItemsData as $collectionItemData) {
            $item = null;

            try {
                $item = $this->itemLoader->loadByRemoteId(
                    $collectionItemData['value_id'],
                    $collectionItemData['value_type']
                );
            } catch (ItemException $e) {
                // Do nothing
            }

            $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                $this->mapItemType($collectionItemData['type']),
                $item instanceof ItemInterface ? $item->getValueId() : null,
                $collectionItemData['value_type']
            );

            $this->collectionService->addItem($collection, $itemCreateStruct, $collectionItemData['position']);
        }
    }

    /**
     * Map item's exported type string to the real type value.
     *
     * @param string $typeString Item exported type string
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If type string is not recognized
     *
     * @return int
     */
    private function mapItemType($typeString)
    {
        switch ($typeString) {
            case 'MANUAL':
                return Item::TYPE_MANUAL;
            case 'OVERRIDE':
                return Item::TYPE_OVERRIDE;
        }

        throw new RuntimeException(sprintf("Unknown item type '%s'", $typeString));
    }
}
