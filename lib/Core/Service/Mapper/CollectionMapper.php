<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;

final class CollectionMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    private $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    private $configMapper;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface
     */
    private $itemDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    private $queryTypeRegistry;

    public function __construct(
        CollectionHandlerInterface $collectionHandler,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        ItemDefinitionRegistryInterface $itemDefinitionRegistry,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->itemDefinitionRegistry = $itemDefinitionRegistry;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Builds the API collection value object from persistence one.
     *
     * If not empty, the first available locale in $locales array will be returned.
     *
     * If the collection is always available and $useMainLocale is set to true,
     * collection in main locale will be returned if none of the locales in $locales
     * array are found.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string[] $locales
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the collection does not have any requested translations
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function mapCollection(PersistenceCollection $collection, array $locales = null, $useMainLocale = true)
    {
        $locales = !empty($locales) ? $locales : array($collection->mainLocale);
        if ($useMainLocale && $collection->alwaysAvailable) {
            $locales[] = $collection->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $collection->availableLocales));
        if (empty($validLocales)) {
            throw new NotFoundException('collection', $collection->id);
        }

        $collectionLocale = reset($validLocales);

        $persistenceItems = $this->collectionHandler->loadCollectionItems($collection);

        $items = array();
        foreach ($persistenceItems as $persistenceItem) {
            $items[] = $this->mapItem($persistenceItem);
        }

        $query = null;
        $persistenceQuery = null;
        $type = Collection::TYPE_MANUAL;

        try {
            $persistenceQuery = $this->collectionHandler->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        if ($persistenceQuery instanceof PersistenceQuery) {
            $query = $this->mapQuery($persistenceQuery, $locales, false);
            $type = Collection::TYPE_DYNAMIC;
        }

        $collectionData = array(
            'id' => $collection->id,
            'status' => $collection->status,
            'offset' => $collection->offset,
            'limit' => $collection->limit,
            'type' => $type,
            'items' => $items,
            'query' => $query,
            'published' => $collection->status === Value::STATUS_PUBLISHED,
            'isTranslatable' => $collection->isTranslatable,
            'mainLocale' => $collection->mainLocale,
            'alwaysAvailable' => $collection->alwaysAvailable,
            'availableLocales' => $collection->availableLocales,
            'locale' => $collectionLocale,
        );

        return new Collection($collectionData);
    }

    /**
     * Builds the API item value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function mapItem(PersistenceItem $item)
    {
        $itemDefinition = $this->itemDefinitionRegistry->getItemDefinition($item->valueType);

        $itemData = array(
            'id' => $item->id,
            'status' => $item->status,
            'collectionId' => $item->collectionId,
            'position' => $item->position,
            'type' => $item->type,
            'valueId' => $item->valueId,
            'valueType' => $item->valueType,
            'published' => $item->status === Value::STATUS_PUBLISHED,
            'definition' => $itemDefinition,
            'configs' => $this->configMapper->mapConfig($item->config, $itemDefinition->getConfigDefinitions()),
        );

        return new Item($itemData);
    }

    /**
     * Builds the API query value object from persistence one.
     *
     * If not empty, the first available locale in $locales array will be returned.
     *
     * If the query is always available and $useMainLocale is set to true,
     * query in main locale will be returned if none of the locales in $locales
     * array are found.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param string[] $locales
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the query does not have any requested locales
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function mapQuery(PersistenceQuery $query, array $locales = null, $useMainLocale = true)
    {
        $queryType = $this->queryTypeRegistry->getQueryType($query->type);

        $locales = !empty($locales) ? $locales : array($query->mainLocale);
        if ($useMainLocale && $query->alwaysAvailable) {
            $locales[] = $query->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $query->availableLocales));
        if (empty($validLocales)) {
            throw new NotFoundException('query', $query->id);
        }

        $queryLocale = reset($validLocales);
        $untranslatableParams = $this->parameterMapper->extractUntranslatableParameters(
            $queryType,
            $query->parameters[$query->mainLocale]
        );

        $queryData = array(
            'id' => $query->id,
            'status' => $query->status,
            'collectionId' => $query->collectionId,
            'queryType' => $queryType,
            'published' => $query->status === Value::STATUS_PUBLISHED,
            'isTranslatable' => $query->isTranslatable,
            'mainLocale' => $query->mainLocale,
            'alwaysAvailable' => $query->alwaysAvailable,
            'availableLocales' => $query->availableLocales,
            'locale' => $queryLocale,
            'parameters' => $this->parameterMapper->mapParameters(
                $queryType,
                $untranslatableParams + $query->parameters[$queryLocale]
            ),
        );

        return new Query($queryData);
    }
}
