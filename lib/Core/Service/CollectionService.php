<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct as APICollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct as APIItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct as APIItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct as APIQueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct as APIQueryUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\ConfigMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\HandlerInterface;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryTranslationUpdateStruct;

final class CollectionService extends Service implements APICollectionService
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    private $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder
     */
    private $structBuilder;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    private $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    private $configMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    private $handler;

    public function __construct(
        HandlerInterface $persistenceHandler,
        CollectionValidator $validator,
        CollectionMapper $mapper,
        CollectionStructBuilder $structBuilder,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;

        $this->handler = $persistenceHandler->getCollectionHandler();
    }

    public function loadCollection($collectionId, array $locales = null, $useMainLocale = true)
    {
        $this->validator->validateId($collectionId, 'collectionId');

        return $this->mapper->mapCollection(
            $this->handler->loadCollection(
                $collectionId,
                Value::STATUS_PUBLISHED
            ),
            $locales,
            $useMainLocale
        );
    }

    public function loadCollectionDraft($collectionId, array $locales = null, $useMainLocale = true)
    {
        $this->validator->validateId($collectionId, 'collectionId');

        return $this->mapper->mapCollection(
            $this->handler->loadCollection(
                $collectionId,
                Value::STATUS_DRAFT
            ),
            $locales,
            $useMainLocale
        );
    }

    public function updateCollection(Collection $collection, APICollectionUpdateStruct $collectionUpdateStruct)
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Only draft collections can be updated.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validateCollectionUpdateStruct($collection, $collectionUpdateStruct);

        $updatedCollection = $this->transaction(
            function () use ($persistenceCollection, $collectionUpdateStruct) {
                return $this->handler->updateCollection(
                    $persistenceCollection,
                    new CollectionUpdateStruct(
                        [
                            'offset' => $collectionUpdateStruct->offset,
                            'limit' => $collectionUpdateStruct->limit,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapCollection($updatedCollection, [$collection->getLocale()]);
    }

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

    public function loadQuery($queryId, array $locales = null, $useMainLocale = true)
    {
        $this->validator->validateId($queryId, 'queryId');

        return $this->mapper->mapQuery(
            $this->handler->loadQuery(
                $queryId,
                Value::STATUS_PUBLISHED
            ),
            $locales,
            $useMainLocale
        );
    }

    public function loadQueryDraft($queryId, array $locales = null, $useMainLocale = true)
    {
        $this->validator->validateId($queryId, 'queryId');

        return $this->mapper->mapQuery(
            $this->handler->loadQuery(
                $queryId,
                Value::STATUS_DRAFT
            ),
            $locales,
            $useMainLocale
        );
    }

    public function changeCollectionType(Collection $collection, $newType, APIQueryCreateStruct $queryCreateStruct = null)
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Type can be changed only for draft collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        if (!in_array($newType, [Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC], true)) {
            throw new BadStateException('newType', 'New collection type must be manual or dynamic.');
        }

        if ($newType === Collection::TYPE_DYNAMIC && $queryCreateStruct === null) {
            throw new BadStateException('queryCreateStruct', 'Query create struct must be defined when converting to dynamic collection.');
        }

        $this->transaction(
            function () use ($persistenceCollection, $newType, $queryCreateStruct) {
                $this->handler->deleteCollectionQuery($persistenceCollection);

                if ($newType === Collection::TYPE_MANUAL) {
                    $persistenceCollection = $this->handler->updateCollection(
                        $persistenceCollection,
                        new CollectionUpdateStruct(
                            [
                                'offset' => 0,
                            ]
                        )
                    );

                    foreach ($this->handler->loadCollectionItems($persistenceCollection) as $index => $item) {
                        $this->handler->moveItem($item, $index);
                    }
                } elseif ($newType === Collection::TYPE_DYNAMIC) {
                    $this->handler->createQuery(
                        $persistenceCollection,
                        new QueryCreateStruct(
                            [
                                'type' => $queryCreateStruct->queryType->getType(),
                                'parameters' => $this->parameterMapper->serializeValues(
                                    $queryCreateStruct->queryType,
                                    $queryCreateStruct->getParameterValues()
                                ),
                            ]
                        )
                    );
                }
            }
        );

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        return $this->mapper->mapCollection($persistenceCollection, [$collection->getLocale()]);
    }

    public function addItem(Collection $collection, APIItemCreateStruct $itemCreateStruct, $position = null)
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Items can only be added to draft collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition(
            $position,
            'position',
            $collection->hasQuery()
        );

        $this->validator->validateItemCreateStruct($itemCreateStruct);

        $createdItem = $this->transaction(
            function () use ($persistenceCollection, $position, $itemCreateStruct) {
                return $this->handler->addItem(
                    $persistenceCollection,
                    new ItemCreateStruct(
                        [
                            'position' => $position,
                            'value' => $itemCreateStruct->value,
                            'valueType' => $itemCreateStruct->definition->getValueType(),
                            'type' => $itemCreateStruct->type,
                            'config' => $this->configMapper->serializeValues(
                                $itemCreateStruct->getConfigStructs(),
                                $itemCreateStruct->definition->getConfigDefinitions()
                            ),
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapItem($createdItem);
    }

    public function updateItem(Item $item, APIItemUpdateStruct $itemUpdateStruct)
    {
        if (!$item->isDraft()) {
            throw new BadStateException('item', 'Only draft items can be updated.');
        }

        $persistenceItem = $this->handler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->validator->validateItemUpdateStruct($item, $itemUpdateStruct);

        $itemDefinition = $item->getDefinition();

        $updatedItem = $this->transaction(
            function () use ($itemDefinition, $persistenceItem, $itemUpdateStruct) {
                return $this->handler->updateItem(
                    $persistenceItem,
                    new ItemUpdateStruct(
                        [
                            'config' => $this->configMapper->serializeValues(
                                $itemUpdateStruct->getConfigStructs(),
                                $itemDefinition->getConfigDefinitions(),
                                $persistenceItem->config
                            ),
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapItem($updatedItem);
    }

    public function moveItem(Item $item, $position)
    {
        if (!$item->isDraft()) {
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

    public function deleteItem(Item $item)
    {
        if (!$item->isDraft()) {
            throw new BadStateException('item', 'Only draft items can be deleted.');
        }

        $persistenceItem = $this->handler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceItem) {
                $this->handler->deleteItem($persistenceItem);
            }
        );
    }

    public function deleteItems(Collection $collection, $itemType = null)
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Only items in draft collections can be deleted.');
        }

        if ($itemType !== null) {
            if (!in_array($itemType, [Item::TYPE_MANUAL, Item::TYPE_OVERRIDE], true)) {
                throw new BadStateException('itemType', 'Provided item type is not valid.');
            }
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $updatedCollection = $this->transaction(
            function () use ($persistenceCollection, $itemType) {
                return $this->handler->deleteItems($persistenceCollection, $itemType);
            }
        );

        return $this->mapper->mapCollection($updatedCollection, [$collection->getLocale()]);
    }

    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        if (!$query->isDraft()) {
            throw new BadStateException('query', 'Only draft queries can be updated.');
        }

        $persistenceQuery = $this->handler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->validator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if (!in_array($queryUpdateStruct->locale, $persistenceQuery->availableLocales, true)) {
            throw new BadStateException('query', 'Query does not have the specified translation.');
        }

        $queryType = $query->getQueryType();

        $updatedQuery = $this->transaction(
            function () use ($queryType, $persistenceQuery, $queryUpdateStruct) {
                return $this->updateQueryTranslations(
                    $queryType,
                    $persistenceQuery,
                    $queryUpdateStruct
                );
            }
        );

        return $this->mapper->mapQuery($updatedQuery, [$query->getLocale()]);
    }

    public function newCollectionCreateStruct(APIQueryCreateStruct $queryCreateStruct = null)
    {
        return $this->structBuilder->newCollectionCreateStruct($queryCreateStruct);
    }

    public function newCollectionUpdateStruct(Collection $collection = null)
    {
        return $this->structBuilder->newCollectionUpdateStruct($collection);
    }

    public function newItemCreateStruct(ItemDefinitionInterface $itemDefinition, $type, $value)
    {
        return $this->structBuilder->newItemCreateStruct($itemDefinition, $type, $value);
    }

    public function newItemUpdateStruct(Item $item = null)
    {
        return $this->structBuilder->newItemUpdateStruct($item);
    }

    public function newQueryCreateStruct(QueryTypeInterface $queryType)
    {
        return $this->structBuilder->newQueryCreateStruct($queryType);
    }

    public function newQueryUpdateStruct($locale, Query $query = null)
    {
        return $this->structBuilder->newQueryUpdateStruct($locale, $query);
    }

    /**
     * Updates translations for specified query.
     *
     * This makes sure that untranslatable parameters are always kept in sync between all
     * available translations in the query. This means that if main translation is updated,
     * all other translations need to be updated too to reflect changes to untranslatable params,
     * and if any other translation is updated, it needs to take values of untranslatable params
     * from the main translation.
     *
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface $queryType
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $persistenceQuery
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    private function updateQueryTranslations(QueryTypeInterface $queryType, PersistenceQuery $persistenceQuery, APIQueryUpdateStruct $queryUpdateStruct)
    {
        if ($queryUpdateStruct->locale === $persistenceQuery->mainLocale) {
            $persistenceQuery = $this->handler->updateQueryTranslation(
                $persistenceQuery,
                $queryUpdateStruct->locale,
                new QueryTranslationUpdateStruct(
                    [
                        'parameters' => $this->parameterMapper->serializeValues(
                            $queryType,
                            $queryUpdateStruct->getParameterValues(),
                            $persistenceQuery->parameters[$persistenceQuery->mainLocale]
                        ),
                    ]
                )
            );
        }

        $untranslatableParams = $this->parameterMapper->extractUntranslatableParameters(
            $queryType,
            $persistenceQuery->parameters[$persistenceQuery->mainLocale]
        );

        $localesToUpdate = [$queryUpdateStruct->locale];
        if ($persistenceQuery->mainLocale === $queryUpdateStruct->locale) {
            $localesToUpdate = $persistenceQuery->availableLocales;

            // Remove the main locale from the array, since we already updated that one
            array_splice($localesToUpdate, array_search($persistenceQuery->mainLocale, $persistenceQuery->availableLocales, true), 1);
        }

        foreach ($localesToUpdate as $locale) {
            $params = $persistenceQuery->parameters[$locale];

            if ($locale === $queryUpdateStruct->locale) {
                $params = $this->parameterMapper->serializeValues(
                    $queryType,
                    $queryUpdateStruct->getParameterValues(),
                    $params
                );
            }

            $persistenceQuery = $this->handler->updateQueryTranslation(
                $persistenceQuery,
                $locale,
                new QueryTranslationUpdateStruct(
                    [
                        'parameters' => $untranslatableParams + $params,
                    ]
                )
            );
        }

        return $persistenceQuery;
    }
}
