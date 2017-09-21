<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;

class CollectionMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    private $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    private $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    private $queryTypeRegistry;

    public function __construct(
        CollectionHandler $collectionHandler,
        ParameterMapper $parameterMapper,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->parameterMapper = $parameterMapper;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Builds the API collection value object from persistence one.
     *
     * If $locale is a string, the collection is loaded in specified locale.
     * If $locale is an array of strings, the first available locale will
     * be returned.
     *
     * If the collection is always available and $useMainLocale is set to true,
     * collection in main locale will be returned if none of the locales in $locale
     * array are found.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string|string[] $locale
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the collection does not have any requested translations
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function mapCollection(PersistenceCollection $collection, $locale = null, $useMainLocale = true)
    {
        if (!is_array($locale)) {
            $locale = array(is_string($locale) ? $locale : $collection->mainLocale);
            $useMainLocale = false;
        }

        if ($useMainLocale && $collection->alwaysAvailable) {
            $locale[] = $collection->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locale, $collection->availableLocales));
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
            $query = $this->mapQuery($persistenceQuery, $locale, false);
            $type = Collection::TYPE_DYNAMIC;
        }

        $collectionData = array(
            'id' => $collection->id,
            'status' => $collection->status,
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
        $itemData = array(
            'id' => $item->id,
            'status' => $item->status,
            'collectionId' => $item->collectionId,
            'position' => $item->position,
            'type' => $item->type,
            'valueId' => $item->valueId,
            'valueType' => $item->valueType,
            'published' => $item->status === Value::STATUS_PUBLISHED,
        );

        return new Item($itemData);
    }

    /**
     * Builds the API query value object from persistence one.
     *
     * If $locale is a string, the query is loaded in specified locale.
     * If $locale is an array of strings, the first available locale will
     * be returned.
     *
     * If the query is always available and $useMainLocale is set to true,
     * query in main locale will be returned if none of the locales in $locale
     * array are found.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param string|string[] $locale
     * @param bool $useMainLocale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the query does not have any requested locales
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function mapQuery(PersistenceQuery $query, $locale = null, $useMainLocale = true)
    {
        $queryType = $this->queryTypeRegistry->getQueryType($query->type);

        if (!is_array($locale)) {
            $locale = array(is_string($locale) ? $locale : $query->mainLocale);
            $useMainLocale = false;
        }

        if ($useMainLocale && $query->alwaysAvailable) {
            $locale[] = $query->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locale, $query->availableLocales));
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
