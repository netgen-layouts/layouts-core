<?php

namespace Netgen\BlockManager\Transfer\Input\DataHandler;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Exception\RuntimeException;
use Exception;

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
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry
     */
    private $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry
     */
    private $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    public function __construct(
        BlockService $blockService,
        BlockDefinitionRegistry $blockDefinitionRegistry,
        CollectionService $collectionService,
        LayoutService $layoutService,
        LayoutTypeRegistry $layoutTypeRegistry,
        QueryTypeRegistry $queryTypeRegistry
    ) {
        $this->blockService = $blockService;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->collectionService = $collectionService;
        $this->layoutService = $layoutService;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->queryTypeRegistry = $queryTypeRegistry;
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
            $data['name'],
            $data['main_locale']
        );
        $createStruct->description = $data['description'];
        $createStruct->shared = $data['is_shared'];

        $this->layoutService->beginTransaction();

        try {
            $layoutDraft = $this->layoutService->createLayout($createStruct);
            $this->processZones($layoutDraft, $data);
            $layout = $this->layoutService->publishLayout($layoutDraft);
            $this->layoutService->commitTransaction();

            return $layout;
        } catch (Exception $e) {
            $this->layoutService->rollbackTransaction();
            throw $e;
        }
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
        $blockDataMapByZone = array();

        foreach ($layout->getZones() as $zone) {
            if (!array_key_exists($zone->getIdentifier(), $layoutData['zones'])) {
                throw new RuntimeException("Missing data for zone '{$zone->getIdentifier()}'");
            }

            $blockDataMapByZone[] = $this->processZone($zone, $layoutData['zones'][$zone->getIdentifier()]);
        }

        $this->addTranslations($layout, $layoutData);
        $blockDataMap = array_merge(...$blockDataMapByZone);
        $this->updateTranslations($blockDataMap);
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
     * Update translations from the given $blockData.
     *
     * @see createBlocks()
     *
     * @param array $blockDataMap Block data as returned by createBlocks()
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If translation data is not consistent
     * @throws \Exception If thrown by the underlying API
     */
    private function updateTranslations(array $blockDataMap)
    {
        foreach ($blockDataMap as $data) {
            $id = $data['id'];
            $translationsData = $data['data']['parameters'];

            $this->updateBlockTranslations($id, $translationsData);
        }
    }

    /**
     * Update all translations of the block with the given $id with $parameterData.
     *
     * @param int|string $id Block id
     * @param array $translationsData Block parameters data by translation locale
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If translation data is not consistent
     * @throws \Exception If thrown by the underlying API
     */
    private function updateBlockTranslations($id, array $translationsData)
    {
        $blockDraft = $this->blockService->loadBlockDraft($id);
        $mainLocale = $blockDraft->getMainLocale();

        foreach ($blockDraft->getAvailableLocales() as $locale) {
            if ($locale === $mainLocale) {
                continue;
            }

            if (!array_key_exists($locale, $translationsData)) {
                throw new RuntimeException("Could not find locale '{$locale}' in the given data");
            }

            $this->updateBlockTranslation($blockDraft, $translationsData[$locale], $locale);
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
        $updateStruct->fillFromHash($block->getDefinition(), $parameterData);

        $this->blockService->updateBlock($block, $updateStruct);
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
     *
     * @return array Block data map as returned by createBlocks()
     */
    private function processZone(Zone $zone, array $zoneData)
    {
        $blockDataSet = array();

        if ($zoneData['linked_zone'] === null) {
            $blockDataSet = $this->createBlocks($zone, $zoneData['blocks']);
        } else {
            $this->linkZone($zone, $zoneData['linked_zone']);
        }

        return $blockDataSet;
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
     * Create blocks in the given $zone from the given $blocksData and return mapped
     * data for translation.
     *
     * Note that we need to return data map for translation here where the block is created,
     * otherwise we would not have a way to connect the data with the corresponding block.
     *
     * Code example:
     *  <code>
     *      [
     *          [
     *              'id' => 42,
     *              'data' => [...]
     *          ],
     *          ...
     *      ]
     *  </code>
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param array $blocksData
     *
     * @throws \Exception If thrown by the underlying API
     *
     * @return array An array of block data, as an array with ID and corresponding data
     */
    private function createBlocks(Zone $zone, array $blocksData)
    {
        $blockDataMap = array();

        foreach ($blocksData as $blockData) {
            $block = $this->createBlock($zone, $blockData);
            $blockDataMap[] = [
                'id' => $block->getId(),
                'data' => $blockData,
            ];
        }

        return $blockDataMap;
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
    private function createBlock(Zone $zone, array $blockData)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($blockData['definition_identifier']);

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->name = $blockData['name'];
        $blockCreateStruct->viewType = $blockData['view_type'];
        $blockCreateStruct->itemViewType = $blockData['item_view_type'];
        $blockCreateStruct->isTranslatable = $blockData['is_translatable'];
        $blockCreateStruct->alwaysAvailable = $blockData['is_always_available'];
        $blockCreateStruct->fillFromHash($blockDefinition, $blockData['parameters'][$blockData['main_locale']]);
        $this->setConfigStructs($blockCreateStruct, $blockDefinition, $blockData['configuration']);

        $block = $this->blockService->createBlockInZone($blockCreateStruct, $zone);

        $this->processCollections($block, $blockData['collections']);

        return $block;
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
        $configDefinitionMap = [];

        foreach ($configDefinitions as $configDefinition) {
            $configDefinitionMap[$configDefinition->getConfigKey()] = $configDefinition;
        }

        foreach ($configurationData as $configKey => $hash) {
            $configStruct = new ConfigStruct();
            $configStruct->fillFromHash($configDefinitionMap[$configKey], $hash);
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

        // We can only have one collection ATM, the 'default' collection
        $collectionReference = $block->getCollectionReferences()['default'];
        $collection = $collectionReference->getCollection();
        $collectionData = $collectionsData['default'];

        $this->setCollectionQuery($collection, $collectionData);
        $this->createManualItems($collection, $collectionData['manual_items']);
    }

    /**
     * Creates a query in the given $collection from the $collectionData.
     *
     * @see \Netgen\BlockManager\API\Values\Collection\Query
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param array $collectionData
     *
     * @throws \Exception If thrown by the underlying API
     */
    private function setCollectionQuery(Collection $collection, array $collectionData)
    {
        $collectionType = $this->mapCollectionType($collectionData['type']);

        if ($collectionType !== Collection::TYPE_DYNAMIC || $collectionData['query'] === null) {
            return;
        }

        $queryTypeIdentifier = $collectionData['query']['query_type'];
        $queryType = $this->queryTypeRegistry->getQueryType($queryTypeIdentifier);
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct($queryType);
        $queryCreateStruct->fillFromHash($queryType, $collectionData['query']['parameters']);

        $this->collectionService->changeCollectionType(
            $collection,
            $collectionType,
            $queryCreateStruct
        );
    }

    /**
     * Create items in the $collection from the given $itemsData.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param array $itemsData
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException
     * @throws \Netgen\BlockManager\Exception\RuntimeException
     */
    private function createManualItems(Collection $collection, array $itemsData)
    {
        foreach ($itemsData as $itemData) {
            $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                $this->mapItemType($itemData['type']),
                $itemData['value_id'],
                $itemData['value_type']
            );

            $this->collectionService->addItem($collection, $itemCreateStruct, $itemData['position']);
        }
    }

    /**
     * Map collection's exported type string to the real type value.
     *
     * @param string $typeString Collection exported type string
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If type string is not recognized
     *
     * @return int
     */
    private function mapCollectionType($typeString)
    {
        switch ($typeString) {
            case 'MANUAL':
                return Collection::TYPE_MANUAL;
            case 'DYNAMIC':
                return Collection::TYPE_DYNAMIC;
        }

        throw new RuntimeException("Unknown collection type '{$typeString}'");
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

        throw new RuntimeException("Unknown item type '{$typeString}'");
    }
}
