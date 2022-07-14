<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Service;

use Netgen\Layouts\API\Service\CollectionService as APICollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct as APICollectionCreateStruct;
use Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct as APICollectionUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemCreateStruct as APIItemCreateStruct;
use Netgen\Layouts\API\Values\Collection\ItemUpdateStruct as APIItemUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct as APIQueryCreateStruct;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct as APIQueryUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotCreateStruct as APISlotCreateStruct;
use Netgen\Layouts\API\Values\Collection\SlotUpdateStruct as APISlotUpdateStruct;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Core\Mapper\CollectionMapper;
use Netgen\Layouts\Core\Mapper\ConfigMapper;
use Netgen\Layouts\Core\Mapper\ParameterMapper;
use Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder;
use Netgen\Layouts\Core\Validator\CollectionValidator;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\Layouts\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\Layouts\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\Layouts\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\QueryTranslationUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Slot as PersistenceSlot;
use Netgen\Layouts\Persistence\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\SlotUpdateStruct;
use Ramsey\Uuid\UuidInterface;

use function array_search;
use function array_splice;
use function in_array;
use function is_int;
use function iterator_to_array;

final class CollectionService implements APICollectionService
{
    use TransactionTrait;

    private CollectionValidator $validator;

    private CollectionMapper $mapper;

    private CollectionStructBuilder $structBuilder;

    private ParameterMapper $parameterMapper;

    private ConfigMapper $configMapper;

    private CollectionHandlerInterface $collectionHandler;

    public function __construct(
        TransactionHandlerInterface $transactionHandler,
        CollectionValidator $validator,
        CollectionMapper $mapper,
        CollectionStructBuilder $structBuilder,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        CollectionHandlerInterface $collectionHandler
    ) {
        $this->transactionHandler = $transactionHandler;

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->parameterMapper = $parameterMapper;
        $this->configMapper = $configMapper;
        $this->collectionHandler = $collectionHandler;
    }

    public function loadCollection(UuidInterface $collectionId, ?array $locales = null, bool $useMainLocale = true): Collection
    {
        return $this->mapper->mapCollection(
            $this->collectionHandler->loadCollection(
                $collectionId,
                Value::STATUS_PUBLISHED,
            ),
            $locales,
            $useMainLocale,
        );
    }

    public function loadCollectionDraft(UuidInterface $collectionId, ?array $locales = null, bool $useMainLocale = true): Collection
    {
        return $this->mapper->mapCollection(
            $this->collectionHandler->loadCollection(
                $collectionId,
                Value::STATUS_DRAFT,
            ),
            $locales,
            $useMainLocale,
        );
    }

    public function updateCollection(Collection $collection, APICollectionUpdateStruct $collectionUpdateStruct): Collection
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Only draft collections can be updated.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validateCollectionUpdateStruct($collection, $collectionUpdateStruct);

        $updatedCollection = $this->transaction(
            fn (): PersistenceCollection => $this->collectionHandler->updateCollection(
                $persistenceCollection,
                CollectionUpdateStruct::fromArray(
                    [
                        'offset' => $collectionUpdateStruct->offset,
                        'limit' => $collectionUpdateStruct->limit,
                    ],
                ),
            ),
        );

        return $this->mapper->mapCollection($updatedCollection, [$collection->getLocale()]);
    }

    public function loadItem(UuidInterface $itemId): Item
    {
        return $this->mapper->mapItem(
            $this->collectionHandler->loadItem(
                $itemId,
                Value::STATUS_PUBLISHED,
            ),
        );
    }

    public function loadItemDraft(UuidInterface $itemId): Item
    {
        return $this->mapper->mapItem(
            $this->collectionHandler->loadItem(
                $itemId,
                Value::STATUS_DRAFT,
            ),
        );
    }

    public function loadQuery(UuidInterface $queryId, ?array $locales = null, bool $useMainLocale = true): Query
    {
        return $this->mapper->mapQuery(
            $this->collectionHandler->loadQuery(
                $queryId,
                Value::STATUS_PUBLISHED,
            ),
            $locales,
            $useMainLocale,
        );
    }

    public function loadQueryDraft(UuidInterface $queryId, ?array $locales = null, bool $useMainLocale = true): Query
    {
        return $this->mapper->mapQuery(
            $this->collectionHandler->loadQuery(
                $queryId,
                Value::STATUS_DRAFT,
            ),
            $locales,
            $useMainLocale,
        );
    }

    public function loadSlot(UuidInterface $slotId): Slot
    {
        return $this->mapper->mapSlot(
            $this->collectionHandler->loadSlot(
                $slotId,
                Value::STATUS_PUBLISHED,
            ),
        );
    }

    public function loadSlotDraft(UuidInterface $slotId): Slot
    {
        return $this->mapper->mapSlot(
            $this->collectionHandler->loadSlot(
                $slotId,
                Value::STATUS_DRAFT,
            ),
        );
    }

    public function changeCollectionType(Collection $collection, int $newType, ?APIQueryCreateStruct $queryCreateStruct = null): Collection
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Type can be changed only for draft collections.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        if (!in_array($newType, [Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC], true)) {
            throw new BadStateException('newType', 'New collection type must be manual or dynamic.');
        }

        if ($newType === Collection::TYPE_DYNAMIC && $queryCreateStruct === null) {
            throw new BadStateException(
                'queryCreateStruct',
                'Query create struct must be defined when converting to dynamic collection.',
            );
        }

        $this->transaction(
            function () use ($persistenceCollection, $newType, $queryCreateStruct): void {
                $this->collectionHandler->deleteCollectionQuery($persistenceCollection);

                if ($newType === Collection::TYPE_MANUAL) {
                    $persistenceCollection = $this->collectionHandler->updateCollection(
                        $persistenceCollection,
                        CollectionUpdateStruct::fromArray(
                            [
                                'offset' => 0,
                            ],
                        ),
                    );

                    foreach ($this->collectionHandler->loadCollectionItems($persistenceCollection) as $index => $item) {
                        $this->collectionHandler->moveItem($item, $index);
                    }
                } elseif ($queryCreateStruct !== null) {
                    $queryType = $queryCreateStruct->getQueryType();

                    $this->collectionHandler->createQuery(
                        $persistenceCollection,
                        QueryCreateStruct::fromArray(
                            [
                                'type' => $queryType->getType(),
                                'parameters' => iterator_to_array(
                                    $this->parameterMapper->serializeValues(
                                        $queryType,
                                        $queryCreateStruct->getParameterValues(),
                                    ),
                                ),
                            ],
                        ),
                    );
                }
            },
        );

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        return $this->mapper->mapCollection($persistenceCollection, [$collection->getLocale()]);
    }

    public function addItem(Collection $collection, APIItemCreateStruct $itemCreateStruct, ?int $position = null): Item
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Items can only be added to draft collections.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition(
            $position,
            'position',
            $collection->hasQuery(),
        );

        $this->validator->validateItemCreateStruct($itemCreateStruct);

        $createdItem = $this->transaction(
            fn (): PersistenceItem => $this->collectionHandler->addItem(
                $persistenceCollection,
                ItemCreateStruct::fromArray(
                    [
                        'position' => $position,
                        'value' => $itemCreateStruct->value,
                        'valueType' => $itemCreateStruct->definition->getValueType(),
                        'viewType' => $itemCreateStruct->viewType,
                        'config' => iterator_to_array(
                            $this->configMapper->serializeValues(
                                $itemCreateStruct->getConfigStructs(),
                                $itemCreateStruct->definition->getConfigDefinitions(),
                            ),
                        ),
                    ],
                ),
            ),
        );

        return $this->mapper->mapItem($createdItem);
    }

    public function updateItem(Item $item, APIItemUpdateStruct $itemUpdateStruct): Item
    {
        if (!$item->isDraft()) {
            throw new BadStateException('item', 'Only draft items can be updated.');
        }

        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->validator->validateItemUpdateStruct($item, $itemUpdateStruct);

        $itemDefinition = $item->getDefinition();

        $updatedItem = $this->transaction(
            fn (): PersistenceItem => $this->collectionHandler->updateItem(
                $persistenceItem,
                ItemUpdateStruct::fromArray(
                    [
                        'viewType' => $itemUpdateStruct->viewType,
                        'config' => iterator_to_array(
                            $this->configMapper->serializeValues(
                                $itemUpdateStruct->getConfigStructs(),
                                $itemDefinition->getConfigDefinitions(),
                                $persistenceItem->config,
                            ),
                        ),
                    ],
                ),
            ),
        );

        return $this->mapper->mapItem($updatedItem);
    }

    public function moveItem(Item $item, int $position): Item
    {
        if (!$item->isDraft()) {
            throw new BadStateException('item', 'Only draft items can be moved.');
        }

        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition($position, 'position', true);

        $movedItem = $this->transaction(
            fn (): PersistenceItem => $this->collectionHandler->moveItem(
                $persistenceItem,
                $position,
            ),
        );

        return $this->mapper->mapItem($movedItem);
    }

    public function deleteItem(Item $item): void
    {
        if (!$item->isDraft()) {
            throw new BadStateException('item', 'Only draft items can be deleted.');
        }

        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceItem): void {
                $this->collectionHandler->deleteItem($persistenceItem);
            },
        );
    }

    public function deleteItems(Collection $collection): Collection
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Only items in draft collections can be deleted.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $updatedCollection = $this->transaction(
            fn (): PersistenceCollection => $this->collectionHandler->deleteItems($persistenceCollection),
        );

        return $this->mapper->mapCollection($updatedCollection, [$collection->getLocale()]);
    }

    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct): Query
    {
        if (!$query->isDraft()) {
            throw new BadStateException('query', 'Only draft queries can be updated.');
        }

        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->validator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if (!in_array($queryUpdateStruct->locale, $persistenceQuery->availableLocales, true)) {
            throw new BadStateException('query', 'Query does not have the specified translation.');
        }

        $queryType = $query->getQueryType();

        $updatedQuery = $this->transaction(
            fn (): PersistenceQuery => $this->updateQueryTranslations(
                $queryType,
                $persistenceQuery,
                $queryUpdateStruct,
            ),
        );

        return $this->mapper->mapQuery($updatedQuery, [$query->getLocale()]);
    }

    public function addSlot(Collection $collection, APISlotCreateStruct $slotCreateStruct, int $position): Slot
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Slots can only be added to draft collections.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition($position, 'position', true);

        $createdSlot = $this->transaction(
            fn (): PersistenceSlot => $this->collectionHandler->addSlot(
                $persistenceCollection,
                SlotCreateStruct::fromArray(
                    [
                        'position' => $position,
                        'viewType' => $slotCreateStruct->viewType,
                    ],
                ),
            ),
        );

        return $this->mapper->mapSlot($createdSlot);
    }

    public function updateSlot(Slot $slot, APISlotUpdateStruct $slotUpdateStruct): Slot
    {
        if (!$slot->isDraft()) {
            throw new BadStateException('slot', 'Only draft slots can be updated.');
        }

        $persistenceSlot = $this->collectionHandler->loadSlot($slot->getId(), Value::STATUS_DRAFT);

        $updatedSlot = $this->transaction(
            fn (): PersistenceSlot => $this->collectionHandler->updateSlot(
                $persistenceSlot,
                SlotUpdateStruct::fromArray(
                    [
                        'viewType' => $slotUpdateStruct->viewType,
                    ],
                ),
            ),
        );

        return $this->mapper->mapSlot($updatedSlot);
    }

    public function deleteSlot(Slot $slot): void
    {
        if (!$slot->isDraft()) {
            throw new BadStateException('slot', 'Only draft slots can be deleted.');
        }

        $persistenceSlot = $this->collectionHandler->loadSlot($slot->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceSlot): void {
                $this->collectionHandler->deleteSlot($persistenceSlot);
            },
        );
    }

    public function deleteSlots(Collection $collection): Collection
    {
        if (!$collection->isDraft()) {
            throw new BadStateException('collection', 'Only slots in draft collections can be deleted.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $updatedCollection = $this->transaction(
            fn (): PersistenceCollection => $this->collectionHandler->deleteSlots($persistenceCollection),
        );

        return $this->mapper->mapCollection($updatedCollection, [$collection->getLocale()]);
    }

    public function newCollectionCreateStruct(?APIQueryCreateStruct $queryCreateStruct = null): APICollectionCreateStruct
    {
        return $this->structBuilder->newCollectionCreateStruct($queryCreateStruct);
    }

    public function newCollectionUpdateStruct(?Collection $collection = null): APICollectionUpdateStruct
    {
        return $this->structBuilder->newCollectionUpdateStruct($collection);
    }

    public function newItemCreateStruct(ItemDefinitionInterface $itemDefinition, $value): APIItemCreateStruct
    {
        return $this->structBuilder->newItemCreateStruct($itemDefinition, $value);
    }

    public function newItemUpdateStruct(?Item $item = null): APIItemUpdateStruct
    {
        return $this->structBuilder->newItemUpdateStruct($item);
    }

    public function newQueryCreateStruct(QueryTypeInterface $queryType): APIQueryCreateStruct
    {
        return $this->structBuilder->newQueryCreateStruct($queryType);
    }

    public function newQueryUpdateStruct(string $locale, ?Query $query = null): APIQueryUpdateStruct
    {
        return $this->structBuilder->newQueryUpdateStruct($locale, $query);
    }

    public function newSlotCreateStruct(): APISlotCreateStruct
    {
        return $this->structBuilder->newSlotCreateStruct();
    }

    public function newSlotUpdateStruct(?Slot $slot = null): APISlotUpdateStruct
    {
        return $this->structBuilder->newSlotUpdateStruct($slot);
    }

    /**
     * Updates translations for specified query.
     *
     * This makes sure that untranslatable parameters are always kept in sync between all
     * available translations in the query. This means that if main translation is updated,
     * all other translations need to be updated too to reflect changes to untranslatable params,
     * and if any other translation is updated, it needs to take values of untranslatable params
     * from the main translation.
     */
    private function updateQueryTranslations(QueryTypeInterface $queryType, PersistenceQuery $persistenceQuery, APIQueryUpdateStruct $queryUpdateStruct): PersistenceQuery
    {
        if ($queryUpdateStruct->locale === $persistenceQuery->mainLocale) {
            $persistenceQuery = $this->collectionHandler->updateQueryTranslation(
                $persistenceQuery,
                $queryUpdateStruct->locale,
                QueryTranslationUpdateStruct::fromArray(
                    [
                        'parameters' => iterator_to_array(
                            $this->parameterMapper->serializeValues(
                                $queryType,
                                $queryUpdateStruct->getParameterValues(),
                                $persistenceQuery->parameters[$persistenceQuery->mainLocale],
                            ),
                        ),
                    ],
                ),
            );
        }

        $untranslatableParams = iterator_to_array(
            $this->parameterMapper->extractUntranslatableParameters(
                $queryType,
                $persistenceQuery->parameters[$persistenceQuery->mainLocale],
            ),
        );

        $localesToUpdate = [$queryUpdateStruct->locale];
        if ($persistenceQuery->mainLocale === $queryUpdateStruct->locale) {
            $localesToUpdate = $persistenceQuery->availableLocales;

            // Remove the main locale from the array, since we already updated that one
            $mainLocaleOffset = array_search($persistenceQuery->mainLocale, $persistenceQuery->availableLocales, true);
            if (is_int($mainLocaleOffset)) {
                array_splice($localesToUpdate, $mainLocaleOffset, 1);
            }
        }

        foreach ($localesToUpdate as $locale) {
            $params = $persistenceQuery->parameters[$locale];

            if ($locale === $queryUpdateStruct->locale) {
                $params = iterator_to_array(
                    $this->parameterMapper->serializeValues(
                        $queryType,
                        $queryUpdateStruct->getParameterValues(),
                        $params,
                    ),
                );
            }

            $persistenceQuery = $this->collectionHandler->updateQueryTranslation(
                $persistenceQuery,
                $locale,
                QueryTranslationUpdateStruct::fromArray(
                    [
                        'parameters' => $untranslatableParams + $params,
                    ],
                ),
            );
        }

        return $persistenceQuery;
    }
}
