<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Collection\QueryTranslation;
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
     * If $locales is an array, returned collection will only have specified translations.
     * If $locales is true, returned collection will have all translations, otherwise, the main
     * translation will be returned.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the collection does not have any currently available translations
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function mapCollection(PersistenceCollection $collection, $locales = null)
    {
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
            $query = $this->mapQuery($persistenceQuery, $locales);
            $type = Collection::TYPE_DYNAMIC;
        }

        if ($locales === true) {
            $locales = $collection->availableLocales;
            sort($locales);
        } elseif (!is_array($locales) || empty($locales)) {
            $locales = array($collection->mainLocale);
        }

        if ($collection->alwaysAvailable && !in_array($collection->mainLocale, $locales, true)) {
            $locales[] = $collection->mainLocale;
        }

        $locales = array_values(array_intersect($locales, $collection->availableLocales));

        if (empty($locales)) {
            throw new NotFoundException('collection', $collection->id);
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
            'availableLocales' => $locales,
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
     * If $locales is an array, returned query will only have specified translations.
     * If $locales is true, returned query will have all translations, otherwise, the main
     * translation will be returned.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the query does not have any currently available translations
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function mapQuery(PersistenceQuery $query, $locales = null)
    {
        $queryType = $this->queryTypeRegistry->getQueryType(
            $query->type
        );

        if ($locales === true) {
            $locales = $query->availableLocales;
            sort($locales);
        } elseif (!is_array($locales) || empty($locales)) {
            $locales = array($query->mainLocale);
        }

        if ($query->alwaysAvailable && !in_array($query->mainLocale, $locales, true)) {
            $locales[] = $query->mainLocale;
        }

        $translations = array();
        foreach ($locales as $locale) {
            if (in_array($locale, $query->availableLocales, true)) {
                $translations[$locale] = $this->mapQueryTranslation($query, $queryType, $locale);
            }
        }

        if (empty($translations)) {
            throw new NotFoundException('query', $query->id);
        }

        $queryData = array(
            'id' => $query->id,
            'status' => $query->status,
            'collectionId' => $query->collectionId,
            'queryType' => $queryType,
            'published' => $query->status === Value::STATUS_PUBLISHED,
            'isTranslatable' => $query->isTranslatable,
            'mainLocale' => $query->mainLocale,
            'alwaysAvailable' => $query->alwaysAvailable,
            'availableLocales' => array_keys($translations),
            'translations' => $translations,
        );

        return new Query($queryData);
    }

    /**
     * Maps the query translation for the provided locale.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     * @param string $locale
     *
     * @return \Netgen\BlockManager\Core\Values\Collection\QueryTranslation
     */
    private function mapQueryTranslation(PersistenceQuery $query, QueryTypeInterface $queryType, $locale)
    {
        $untranslatableParams = $this->parameterMapper->extractUntranslatableParameters(
            $queryType,
            $query->parameters[$query->mainLocale]
        );

        return new QueryTranslation(
            array(
                'locale' => $locale,
                'isMainTranslation' => $locale === $query->mainLocale,
                'parameters' => $this->parameterMapper->mapParameters(
                    $queryType,
                    $untranslatableParams + $query->parameters[$locale]
                ),
            )
        );
    }
}
