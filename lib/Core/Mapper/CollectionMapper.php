<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Mapper;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\LazyCollection;
use Netgen\BlockManager\Collection\Item\NullItemDefinition;
use Netgen\BlockManager\Collection\QueryType\NullQueryType;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Exception\Collection\ItemDefinitionException;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
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
     * @var \Netgen\BlockManager\Core\Mapper\ParameterMapper
     */
    private $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Core\Mapper\ConfigMapper
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
     * @var \Netgen\BlockManager\Item\CmsItemLoaderInterface
     */
    private $cmsItemLoader;

    public function __construct(
        CollectionHandlerInterface $collectionHandler,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        ItemDefinitionRegistryInterface $itemDefinitionRegistry,
        QueryTypeRegistryInterface $queryTypeRegistry,
        CmsItemLoaderInterface $cmsItemLoader
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->itemDefinitionRegistry = $itemDefinitionRegistry;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->cmsItemLoader = $cmsItemLoader;
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
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the collection does not have any requested translations
     */
    public function mapCollection(PersistenceCollection $collection, ?array $locales = null, bool $useMainLocale = true): Collection
    {
        $locales = is_array($locales) && count($locales) > 0 ? $locales : [$collection->mainLocale];
        if ($useMainLocale && $collection->alwaysAvailable) {
            $locales[] = $collection->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $collection->availableLocales));
        if (count($validLocales) === 0) {
            throw new NotFoundException('collection', $collection->id);
        }

        $collectionData = [
            'id' => $collection->id,
            'status' => $collection->status,
            'offset' => $collection->offset,
            'limit' => $collection->limit,
            'items' => new LazyCollection(
                function () use ($collection): array {
                    return array_map(
                        function (PersistenceItem $item): Item {
                            return $this->mapItem($item);
                        },
                        $this->collectionHandler->loadCollectionItems($collection)
                    );
                }
            ),
            'query' => function () use ($collection, $locales): ?Query {
                try {
                    $persistenceQuery = $this->collectionHandler->loadCollectionQuery($collection);
                } catch (NotFoundException $e) {
                    return null;
                }

                return $this->mapQuery($persistenceQuery, $locales, false);
            },
            'isTranslatable' => $collection->isTranslatable,
            'mainLocale' => $collection->mainLocale,
            'alwaysAvailable' => $collection->alwaysAvailable,
            'availableLocales' => $collection->availableLocales,
            'locale' => array_values($validLocales)[0],
        ];

        return Collection::fromArray($collectionData);
    }

    /**
     * Builds the API item value from persistence one.
     */
    public function mapItem(PersistenceItem $item): Item
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
            'value' => $item->value,
            'configs' => iterator_to_array(
                $this->configMapper->mapConfig(
                    $item->config,
                    $itemDefinition->getConfigDefinitions()
                )
            ),
            'cmsItem' => function () use ($item, $itemDefinition): CmsItemInterface {
                $valueType = $itemDefinition instanceof NullItemDefinition ? 'null' : $itemDefinition->getValueType();

                return $this->cmsItemLoader->load($item->value, $valueType);
            },
        ];

        return Item::fromArray($itemData);
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
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the query does not have any requested locales
     */
    public function mapQuery(PersistenceQuery $query, ?array $locales = null, bool $useMainLocale = true): Query
    {
        try {
            $queryType = $this->queryTypeRegistry->getQueryType($query->type);
        } catch (QueryTypeException $e) {
            $queryType = new NullQueryType($query->type);
        }

        $locales = is_array($locales) && count($locales) > 0 ? $locales : [$query->mainLocale];
        if ($useMainLocale && $query->alwaysAvailable) {
            $locales[] = $query->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $query->availableLocales));
        if (count($validLocales) === 0) {
            throw new NotFoundException('query', $query->id);
        }

        $queryLocale = array_values($validLocales)[0];
        $untranslatableParams = iterator_to_array(
            $this->parameterMapper->extractUntranslatableParameters(
                $queryType,
                $query->parameters[$query->mainLocale]
            )
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
            'parameters' => iterator_to_array(
                $this->parameterMapper->mapParameters(
                    $queryType,
                    $untranslatableParams + $query->parameters[$queryLocale]
                )
            ),
        ];

        return Query::fromArray($queryData);
    }
}
