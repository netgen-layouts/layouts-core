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
use Netgen\BlockManager\Locale\LocaleContextInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;

class CollectionMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Locale\LocaleContextInterface
     */
    protected $localeContext;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler\CollectionHandler $collectionHandler
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     * @param \Netgen\BlockManager\Locale\LocaleContextInterface $localeContext
     */
    public function __construct(
        CollectionHandler $collectionHandler,
        ParameterMapper $parameterMapper,
        QueryTypeRegistryInterface $queryTypeRegistry,
        LocaleContextInterface $localeContext
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->parameterMapper = $parameterMapper;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->localeContext = $localeContext;
    }

    /**
     * Builds the API collection value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param string[] $locales
     * @param bool $useContext
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the collection does not have any currently available translations
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function mapCollection(PersistenceCollection $collection, array $locales = null, $useContext = true)
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
            $query = $this->mapQuery($persistenceQuery, $locales, $useContext);
            $type = Collection::TYPE_DYNAMIC;
        }

        $collectionLocales = $locales !== null ? $locales : $this->getCollectionLocales($collection, $useContext);
        $collectionLocales = array_values(array_intersect($collectionLocales, $collection->availableLocales));

        if (empty($collectionLocales)) {
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
            'availableLocales' => $collectionLocales,
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
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param string[] $locales
     * @param bool $useContext
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If the query does not have any currently available translations
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function mapQuery(PersistenceQuery $query, array $locales = null, $useContext = true)
    {
        $queryType = $this->queryTypeRegistry->getQueryType(
            $query->type
        );

        $translations = array();
        $queryLocales = $locales !== null ? $locales : $this->getQueryLocales($query, $useContext);

        foreach ($queryLocales as $locale) {
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
    protected function mapQueryTranslation(PersistenceQuery $query, QueryTypeInterface $queryType, $locale)
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

    /**
     * Returns the valid locales for the provided collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param bool $useContext
     *
     * @return string[]
     */
    protected function getCollectionLocales(PersistenceCollection $collection, $useContext = true)
    {
        $locales = $useContext ? $this->localeContext->getLocaleCodes() : $collection->availableLocales;
        if ($collection->alwaysAvailable && !in_array($collection->mainLocale, $locales, true)) {
            $locales[] = $collection->mainLocale;
        }

        return $locales;
    }

    /**
     * Returns the valid locales for the provided query.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param bool $useContext
     *
     * @return string[]
     */
    protected function getQueryLocales(PersistenceQuery $query, $useContext = true)
    {
        $locales = $useContext ? $this->localeContext->getLocaleCodes() : $query->availableLocales;
        if ($query->alwaysAvailable && !in_array($query->mainLocale, $locales, true)) {
            $locales[] = $query->mainLocale;
        }

        return $locales;
    }
}
