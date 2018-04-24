<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Collection\Item\NullItemDefinition;
use Netgen\BlockManager\Collection\NullQueryType;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\LazyCollection;
use Netgen\BlockManager\Exception\Collection\ItemDefinitionException;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
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

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(
        CollectionHandlerInterface $collectionHandler,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        ItemDefinitionRegistryInterface $itemDefinitionRegistry,
        QueryTypeRegistryInterface $queryTypeRegistry,
        ItemLoaderInterface $itemLoader
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->itemDefinitionRegistry = $itemDefinitionRegistry;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->itemLoader = $itemLoader;
    }

    /**
     * Builds the API collection value from persistence one.
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
        $locales = !empty($locales) ? $locales : [$collection->mainLocale];
        if ($useMainLocale && $collection->alwaysAvailable) {
            $locales[] = $collection->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $collection->availableLocales));
        if (empty($validLocales)) {
            throw new NotFoundException('collection', $collection->id);
        }

        $collectionLocale = reset($validLocales);

        $collectionData = [
            'id' => $collection->id,
            'status' => $collection->status,
            'offset' => $collection->offset,
            'limit' => $collection->limit,
            'items' => new LazyCollection(
                function () use ($collection) {
                    return array_map(
                        function (PersistenceItem $item) {
                            return $this->mapItem($item);
                        },
                        $this->collectionHandler->loadCollectionItems($collection)
                    );
                }
            ),
            'query' => function () use ($collection, $locales) {
                try {
                    $persistenceQuery = $this->collectionHandler->loadCollectionQuery($collection);
                } catch (NotFoundException $e) {
                    return;
                }

                return $this->mapQuery($persistenceQuery, $locales, false);
            },
            'isTranslatable' => $collection->isTranslatable,
            'mainLocale' => $collection->mainLocale,
            'alwaysAvailable' => $collection->alwaysAvailable,
            'availableLocales' => $collection->availableLocales,
            'locale' => $collectionLocale,
        ];

        return new Collection($collectionData);
    }

    /**
     * Builds the API item value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function mapItem(PersistenceItem $item)
    {
        try {
            $itemDefinition = $this->itemDefinitionRegistry->getItemDefinition($item->valueType);
        } catch (ItemDefinitionException $e) {
            $itemDefinition = new NullItemDefinition($item->valueType);
        }

        $itemData = [
            'id' => $item->id,
            'status' => $item->status,
            'definition' => $itemDefinition,
            'collectionId' => $item->collectionId,
            'position' => $item->position,
            'type' => $item->type,
            'value' => $item->value,
            'configs' => $this->configMapper->mapConfig($item->config, $itemDefinition->getConfigDefinitions()),
            'cmsItem' => function () use ($item, $itemDefinition) {
                $valueType = $itemDefinition instanceof NullItemDefinition ? 'null' : $itemDefinition->getValueType();

                return $this->itemLoader->load($item->value, $valueType);
            },
        ];

        return new Item($itemData);
    }

    /**
     * Builds the API query value from persistence one.
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
        try {
            $queryType = $this->queryTypeRegistry->getQueryType($query->type);
        } catch (QueryTypeException $e) {
            $queryType = new NullQueryType($query->type);
        }

        $locales = !empty($locales) ? $locales : [$query->mainLocale];
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

        $queryData = [
            'id' => $query->id,
            'status' => $query->status,
            'collectionId' => $query->collectionId,
            'queryType' => $queryType,
            'isTranslatable' => $query->isTranslatable,
            'mainLocale' => $query->mainLocale,
            'alwaysAvailable' => $query->alwaysAvailable,
            'availableLocales' => $query->availableLocales,
            'locale' => $queryLocale,
            'parameters' => $this->parameterMapper->mapParameters(
                $queryType,
                $untranslatableParams + $query->parameters[$queryLocale]
            ),
        ];

        return new Query($queryData);
    }
}
