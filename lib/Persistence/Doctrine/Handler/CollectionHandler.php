<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryTranslationUpdateStruct;

final class CollectionHandler implements CollectionHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler
     */
    private $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper
     */
    private $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    private $positionHelper;

    public function __construct(
        CollectionQueryHandler $queryHandler,
        CollectionMapper $collectionMapper,
        PositionHelper $positionHelper
    ) {
        $this->queryHandler = $queryHandler;
        $this->collectionMapper = $collectionMapper;
        $this->positionHelper = $positionHelper;
    }

    public function loadCollection($collectionId, $status)
    {
        $data = $this->queryHandler->loadCollectionData($collectionId, $status);

        if (empty($data)) {
            throw new NotFoundException('collection', $collectionId);
        }

        $data = $this->collectionMapper->mapCollections($data);

        return reset($data);
    }

    public function loadItem($itemId, $status)
    {
        $data = $this->queryHandler->loadItemData($itemId, $status);

        if (empty($data)) {
            throw new NotFoundException('item', $itemId);
        }

        $data = $this->collectionMapper->mapItems($data);

        return reset($data);
    }

    public function loadCollectionItems(Collection $collection)
    {
        return $this->collectionMapper->mapItems(
            $this->queryHandler->loadCollectionItemsData($collection)
        );
    }

    public function loadQuery($queryId, $status)
    {
        $data = $this->queryHandler->loadQueryData($queryId, $status);

        if (empty($data)) {
            throw new NotFoundException('query', $queryId);
        }

        $query = $this->collectionMapper->mapQuery($data);

        $collection = $this->loadCollection($query->collectionId, $query->status);

        $query->isTranslatable = $collection->isTranslatable;
        $query->mainLocale = $collection->mainLocale;
        $query->alwaysAvailable = $collection->alwaysAvailable;

        return $query;
    }

    public function loadCollectionQuery(Collection $collection)
    {
        $data = $this->queryHandler->loadCollectionQueryData($collection);

        if (empty($data)) {
            throw new NotFoundException('query for collection', $collection->id);
        }

        $query = $this->collectionMapper->mapQuery($data);

        $query->isTranslatable = $collection->isTranslatable;
        $query->mainLocale = $collection->mainLocale;
        $query->alwaysAvailable = $collection->alwaysAvailable;

        return $query;
    }

    public function collectionExists($collectionId, $status)
    {
        return $this->queryHandler->collectionExists($collectionId, $status);
    }

    public function createCollection(CollectionCreateStruct $collectionCreateStruct)
    {
        $newCollection = new Collection(
            array(
                'status' => $collectionCreateStruct->status,
                'isTranslatable' => $collectionCreateStruct->isTranslatable,
                'alwaysAvailable' => $collectionCreateStruct->alwaysAvailable,
                'mainLocale' => $collectionCreateStruct->mainLocale,
                'availableLocales' => array($collectionCreateStruct->mainLocale),
            )
        );

        $newCollection = $this->queryHandler->createCollection($newCollection);

        $this->queryHandler->createCollectionTranslation(
            $newCollection,
            $collectionCreateStruct->mainLocale
        );

        return $newCollection;
    }

    public function createCollectionTranslation(Collection $collection, $locale, $sourceLocale)
    {
        if (in_array($locale, $collection->availableLocales, true)) {
            throw new BadStateException('locale', 'Collection already has the provided locale.');
        }

        if (!in_array($sourceLocale, $collection->availableLocales, true)) {
            throw new BadStateException('locale', 'Collection does not have the provided source locale.');
        }

        $updatedCollection = clone $collection;
        $updatedCollection->availableLocales[] = $locale;

        $this->queryHandler->createCollectionTranslation($collection, $locale);

        $query = null;
        try {
            $query = $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        if ($query instanceof Query) {
            $updatedQuery = clone $query;
            $updatedQuery->availableLocales[] = $locale;
            $updatedQuery->parameters[$locale] = $updatedQuery->parameters[$sourceLocale];

            $this->queryHandler->createQueryTranslation($updatedQuery, $locale);
        }

        return $updatedCollection;
    }

    public function setMainTranslation(Collection $collection, $mainLocale)
    {
        if (!in_array($mainLocale, $collection->availableLocales, true)) {
            throw new BadStateException('mainLocale', 'Collection does not have the provided locale.');
        }

        $updatedCollection = clone $collection;
        $updatedCollection->mainLocale = $mainLocale;

        $this->queryHandler->updateCollection($updatedCollection);

        return $updatedCollection;
    }

    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $updatedCollection = clone $collection;

        if ($collectionUpdateStruct->isTranslatable !== null) {
            $updatedCollection->isTranslatable = (bool) $collectionUpdateStruct->isTranslatable;
        }

        if ($collectionUpdateStruct->alwaysAvailable !== null) {
            $updatedCollection->alwaysAvailable = (bool) $collectionUpdateStruct->alwaysAvailable;
        }

        $this->queryHandler->updateCollection($updatedCollection);

        return $updatedCollection;
    }

    public function copyCollection(Collection $collection)
    {
        $newCollection = clone $collection;
        $newCollection->id = null;

        $newCollection = $this->queryHandler->createCollection($newCollection);

        foreach ($newCollection->availableLocales as $locale) {
            $this->queryHandler->createCollectionTranslation($newCollection, $locale);
        }

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $newItem = clone $collectionItem;

            $newItem->id = null;
            $newItem->collectionId = $newCollection->id;

            $this->queryHandler->addItem($newItem);
        }

        $collectionQuery = null;
        try {
            $collectionQuery = $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        if ($collectionQuery instanceof Query) {
            $newQuery = clone $collectionQuery;

            $newQuery->id = null;
            $newQuery->collectionId = $newCollection->id;

            $this->queryHandler->createQuery($newQuery);

            foreach ($newQuery->availableLocales as $locale) {
                $this->queryHandler->createQueryTranslation($newQuery, $locale);
            }
        }

        return $newCollection;
    }

    public function createCollectionStatus(Collection $collection, $newStatus)
    {
        $newCollection = clone $collection;
        $newCollection->status = $newStatus;

        $this->queryHandler->createCollection($newCollection);

        foreach ($newCollection->availableLocales as $locale) {
            $this->queryHandler->createCollectionTranslation($newCollection, $locale);
        }

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $newItem = clone $collectionItem;
            $newItem->status = $newStatus;

            $this->queryHandler->addItem($newItem);
        }

        $collectionQuery = null;
        try {
            $collectionQuery = $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        if ($collectionQuery instanceof Query) {
            $newQuery = clone $collectionQuery;
            $newQuery->status = $newStatus;

            $this->queryHandler->createQuery($newQuery);

            foreach ($newQuery->availableLocales as $locale) {
                $this->queryHandler->createQueryTranslation($newQuery, $locale);
            }
        }

        return $newCollection;
    }

    public function deleteCollection($collectionId, $status = null)
    {
        $this->queryHandler->deleteCollectionItems($collectionId, $status);

        $queryIds = $this->queryHandler->loadCollectionQueryIds($collectionId, $status);
        $this->queryHandler->deleteQueryTranslations($queryIds, $status);
        $this->queryHandler->deleteQuery($queryIds, $status);

        $this->queryHandler->deleteCollectionTranslations($collectionId, $status);
        $this->queryHandler->deleteCollection($collectionId, $status);
    }

    public function deleteCollectionTranslation(Collection $collection, $locale)
    {
        if (!in_array($locale, $collection->availableLocales, true)) {
            throw new BadStateException('locale', 'Collection does not have the provided locale.');
        }

        if ($locale === $collection->mainLocale) {
            throw new BadStateException('locale', 'Main translation cannot be removed from the collection.');
        }

        $queryIds = $this->queryHandler->loadCollectionQueryIds($collection->id, $collection->status);
        $this->queryHandler->deleteQueryTranslations($queryIds, $collection->status, $locale);

        $this->queryHandler->deleteCollectionTranslations($collection->id, $collection->status, $locale);

        return $this->loadCollection($collection->id, $collection->status);
    }

    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct)
    {
        $isDynamic = true;
        try {
            $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            $isDynamic = false;
        }

        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $collection->status
            ),
            $itemCreateStruct->position,
            $isDynamic
        );

        $newItem = new Item(
            array(
                'collectionId' => $collection->id,
                'position' => $position,
                'type' => $itemCreateStruct->type,
                'valueId' => $itemCreateStruct->valueId,
                'valueType' => $itemCreateStruct->valueType,
                'status' => $collection->status,
            )
        );

        return $this->queryHandler->addItem($newItem);
    }

    public function moveItem(Item $item, $position)
    {
        $collection = $this->loadCollection($item->collectionId, $item->status);

        $isDynamic = true;
        try {
            $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            $isDynamic = false;
        }

        $movedItem = clone $item;

        $movedItem->position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $item->status
            ),
            $item->position,
            $position,
            $isDynamic
        );

        $this->queryHandler->updateItem($movedItem);

        return $movedItem;
    }

    public function deleteItem(Item $item)
    {
        $this->queryHandler->deleteItem($item->id, $item->status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperItemConditions(
                $item->collectionId,
                $item->status
            ),
            $item->position
        );
    }

    public function createQuery(Collection $collection, QueryCreateStruct $queryCreateStruct)
    {
        try {
            $this->loadCollectionQuery($collection);

            throw new BadStateException('collection', 'Provided collection already has a query.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $queryParameters = array();
        foreach ($collection->availableLocales as $collectionLocale) {
            $queryParameters[$collectionLocale] = $queryCreateStruct->parameters;
        }

        $newQuery = new Query(
            array(
                'collectionId' => $collection->id,
                'type' => $queryCreateStruct->type,
                'parameters' => $queryParameters,
                'status' => $collection->status,
                'isTranslatable' => $collection->isTranslatable,
                'alwaysAvailable' => $collection->alwaysAvailable,
                'mainLocale' => $collection->mainLocale,
                'availableLocales' => $collection->availableLocales,
            )
        );

        $newQuery = $this->queryHandler->createQuery($newQuery);

        foreach ($collection->availableLocales as $collectionLocale) {
            $this->queryHandler->createQueryTranslation($newQuery, $collectionLocale);
        }

        return $newQuery;
    }

    public function updateQueryTranslation(Query $query, $locale, QueryTranslationUpdateStruct $translationUpdateStruct)
    {
        $updatedQuery = clone $query;

        if (!in_array($locale, $query->availableLocales, true)) {
            throw new BadStateException('locale', 'Query does not have the provided locale.');
        }

        if (is_array($translationUpdateStruct->parameters)) {
            $updatedQuery->parameters[$locale] = $translationUpdateStruct->parameters;
        }

        $this->queryHandler->updateQueryTranslation($updatedQuery, $locale);

        return $updatedQuery;
    }

    public function deleteCollectionQuery(Collection $collection)
    {
        $queryIds = $this->queryHandler->loadCollectionQueryIds($collection->id, $collection->status);
        $this->queryHandler->deleteQueryTranslations($queryIds, $collection->status);
        $this->queryHandler->deleteQuery($queryIds, $collection->status);
    }

    /**
     * Builds the condition array that will be used with position helper and items in collections.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    private function getPositionHelperItemConditions($collectionId, $status)
    {
        return array(
            'table' => 'ngbm_collection_item',
            'column' => 'position',
            'conditions' => array(
                'collection_id' => $collectionId,
                'status' => $status,
            ),
        );
    }
}
