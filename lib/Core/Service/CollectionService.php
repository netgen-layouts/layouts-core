<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct as APIItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct as APIQueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct as APIQueryUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryTranslationUpdateStruct;

class CollectionService extends Service implements APICollectionService
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder
     */
    protected $structBuilder;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper $mapper
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder $structBuilder
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     */
    public function __construct(
        Handler $persistenceHandler,
        CollectionValidator $validator,
        CollectionMapper $mapper,
        CollectionStructBuilder $structBuilder,
        ParameterMapper $parameterMapper
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->parameterMapper = $parameterMapper;

        $this->handler = $persistenceHandler->getCollectionHandler();
    }

    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollection($collectionId, $locales = null)
    {
        $this->validator->validateId($collectionId, 'collectionId');

        return $this->mapper->mapCollection(
            $this->handler->loadCollection(
                $collectionId,
                Value::STATUS_PUBLISHED
            ),
            $locales
        );
    }

    /**
     * Loads a collection draft with specified ID.
     *
     * @param int|string $collectionId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollectionDraft($collectionId, $locales = null)
    {
        $this->validator->validateId($collectionId, 'collectionId');

        return $this->mapper->mapCollection(
            $this->handler->loadCollection(
                $collectionId,
                Value::STATUS_DRAFT
            ),
            $locales
        );
    }

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItem($itemId)
    {
        $this->validator->validateId($itemId, 'itemId');

        return $this->mapper->mapItem(
            $this->handler->loadItem(
                $itemId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads an item draft with specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItemDraft($itemId)
    {
        $this->validator->validateId($itemId, 'itemId');

        return $this->mapper->mapItem(
            $this->handler->loadItem(
                $itemId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQuery($queryId, $locales = null)
    {
        $this->validator->validateId($queryId, 'queryId');

        return $this->mapper->mapQuery(
            $this->handler->loadQuery(
                $queryId,
                Value::STATUS_PUBLISHED
            ),
            $locales
        );
    }

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     * @param string[]|bool $locales
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQueryDraft($queryId, $locales = null)
    {
        $this->validator->validateId($queryId, 'queryId');

        return $this->mapper->mapQuery(
            $this->handler->loadQuery(
                $queryId,
                Value::STATUS_DRAFT
            ),
            $locales
        );
    }

    /**
     * Changes the type of specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $newType
     * @param \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection type cannot be changed
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function changeCollectionType(Collection $collection, $newType, APIQueryCreateStruct $queryCreateStruct = null)
    {
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Type can be changed only for draft collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        if (!in_array($newType, array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC), true)) {
            throw new BadStateException('newType', 'New collection type must be manual or dynamic.');
        }

        if ($newType === Collection::TYPE_DYNAMIC && $queryCreateStruct === null) {
            throw new BadStateException('queryCreateStruct', 'Query create struct must be defined when converting to dynamic collection.');
        }

        $this->transaction(
            function () use ($collection, $persistenceCollection, $newType, $queryCreateStruct) {
                if ($collection->getType() === Collection::TYPE_DYNAMIC) {
                    $this->handler->deleteCollectionQuery($persistenceCollection);
                }

                if ($newType === Collection::TYPE_MANUAL) {
                    foreach ($this->handler->loadCollectionItems($persistenceCollection) as $index => $item) {
                        $this->handler->moveItem($item, $index);
                    }
                } elseif ($newType === Collection::TYPE_DYNAMIC) {
                    $this->handler->createQuery(
                        $persistenceCollection,
                        new QueryCreateStruct(
                            array(
                                'type' => $queryCreateStruct->queryType->getType(),
                                'parameters' => $this->parameterMapper->serializeValues(
                                    $queryCreateStruct->queryType,
                                    $queryCreateStruct->getParameterValues()
                                ),
                            )
                        )
                    );
                }
            }
        );

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        return $this->mapper->mapCollection($persistenceCollection);
    }

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function addItem(Collection $collection, APIItemCreateStruct $itemCreateStruct, $position = null)
    {
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Items can only be added to draft collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition(
            $position,
            'position',
            $collection->getType() !== Collection::TYPE_MANUAL
        );

        $this->validator->validateItemCreateStruct($itemCreateStruct);

        $createdItem = $this->transaction(
            function () use ($persistenceCollection, $position, $itemCreateStruct) {
                return $this->handler->addItem(
                    $persistenceCollection,
                    new ItemCreateStruct(
                        array(
                            'position' => $position,
                            'valueId' => $itemCreateStruct->valueId,
                            'valueType' => $itemCreateStruct->valueType,
                            'type' => $itemCreateStruct->type,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapItem($createdItem);
    }

    /**
     * Moves an item within the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     *                                                          If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function moveItem(Item $item, $position)
    {
        if ($item->isPublished()) {
            throw new BadStateException('item', 'Only draft items can be moved.');
        }

        $persistenceItem = $this->handler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition($position, 'position', true);

        $movedItem = $this->transaction(
            function () use ($persistenceItem, $position) {
                return $this->handler->moveItem(
                    $persistenceItem,
                    $position
                );
            }
        );

        return $this->mapper->mapItem($movedItem);
    }

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     */
    public function deleteItem(Item $item)
    {
        if ($item->isPublished()) {
            throw new BadStateException('item', 'Only draft items can be deleted.');
        }

        $persistenceItem = $this->handler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceItem) {
                $this->handler->deleteItem($persistenceItem);
            }
        );
    }

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     *                                                          If query does not have a specified translation
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        if ($query->isPublished()) {
            throw new BadStateException('query', 'Only draft queries can be updated.');
        }

        $persistenceQuery = $this->handler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->validator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if (!in_array($queryUpdateStruct->locale, $persistenceQuery->availableLocales, true)) {
            throw new BadStateException('query', 'Query does not have the specified translation.');
        }

        $updatedQuery = $this->transaction(
            function () use ($query, $persistenceQuery, $queryUpdateStruct) {
                return $this->updateQueryTranslations(
                    $query,
                    $persistenceQuery,
                    $queryUpdateStruct
                );
            }
        );

        return $this->mapper->mapQuery($updatedQuery, $query->getAvailableLocales());
    }

    /**
     * Creates a new item create struct.
     *
     * @param int $type
     * @param int|string $valueId
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct
     */
    public function newItemCreateStruct($type, $valueId, $valueType)
    {
        return $this->structBuilder->newItemCreateStruct($type, $valueId, $valueType);
    }

    /**
     * Creates a new query create struct.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType)
    {
        return $this->structBuilder->newQueryCreateStruct($queryType);
    }

    /**
     * Creates a new query update struct.
     *
     * @param string $locale
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct
     */
    public function newQueryUpdateStruct($locale, Query $query = null)
    {
        return $this->structBuilder->newQueryUpdateStruct($locale, $query);
    }

    /**
     * Updates translations for specified queries.
     *
     * This makes sure that untranslatable parameters are always kept in sync between all
     * available translations in the query. This means that if main translations is updated,
     * all other translations need to be updated too to reflect changes to untranslatable params,
     * and if any other translation is updated, it needs to take values of untranslatable params
     * from the main translation.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $persistenceQuery
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    protected function updateQueryTranslations(Query $query, PersistenceQuery $persistenceQuery, APIQueryUpdateStruct $queryUpdateStruct)
    {
        if ($queryUpdateStruct->locale === $persistenceQuery->mainLocale) {
            $persistenceQuery = $this->handler->updateQueryTranslation(
                $persistenceQuery,
                $queryUpdateStruct->locale,
                new QueryTranslationUpdateStruct(
                    array(
                        'parameters' => $this->parameterMapper->serializeValues(
                            $query->getQueryType(),
                            $queryUpdateStruct->getParameterValues(),
                            $persistenceQuery->parameters[$persistenceQuery->mainLocale]
                        ),
                    )
                )
            );
        }

        $untranslatableParams = $this->parameterMapper->extractUntranslatableParameters(
            $query->getQueryType(),
            $persistenceQuery->parameters[$persistenceQuery->mainLocale]
        );

        $localesToUpdate = array($queryUpdateStruct->locale);
        if ($persistenceQuery->mainLocale === $queryUpdateStruct->locale) {
            $localesToUpdate = $persistenceQuery->availableLocales;

            // Remove the main locale from the array, since we already updated that one
            array_splice($localesToUpdate, array_search($persistenceQuery->mainLocale, $persistenceQuery->availableLocales, true), 1);
        }

        foreach ($localesToUpdate as $locale) {
            $params = $persistenceQuery->parameters[$locale];

            if ($locale === $queryUpdateStruct->locale) {
                $params = $this->parameterMapper->serializeValues(
                    $query->getQueryType(),
                    $queryUpdateStruct->getParameterValues(),
                    $params
                );
            }

            $persistenceQuery = $this->handler->updateQueryTranslation(
                $persistenceQuery,
                $locale,
                new QueryTranslationUpdateStruct(
                    array(
                        'parameters' => $untranslatableParams + $params,
                    )
                )
            );
        }

        return $persistenceQuery;
    }
}
