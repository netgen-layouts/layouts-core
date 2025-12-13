<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotList;
use Netgen\Layouts\API\Values\Config\ConfigList;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Collection\Item\NullItemDefinition;
use Netgen\Layouts\Collection\QueryType\NullQueryType;
use Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Exception\Collection\ItemDefinitionException;
use Netgen\Layouts\Exception\Collection\QueryTypeException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\Layouts\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\Layouts\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\Layouts\Persistence\Values\Collection\Slot as PersistenceSlot;
use Symfony\Component\Uid\Uuid;

use function array_first;
use function array_intersect;
use function array_map;
use function array_unique;
use function count;
use function is_array;

final class CollectionMapper
{
    public function __construct(
        private CollectionHandlerInterface $collectionHandler,
        private ParameterMapper $parameterMapper,
        private ConfigMapper $configMapper,
        private ItemDefinitionRegistry $itemDefinitionRegistry,
        private QueryTypeRegistry $queryTypeRegistry,
        private CmsItemLoaderInterface $cmsItemLoader,
    ) {}

    /**
     * Builds the API collection value from persistence one.
     *
     * If not empty, the first available locale in $locales array will be returned.
     *
     * If the collection is always available and $useMainLocale is set to true,
     * collection in main locale will be returned if none of the locales in $locales
     * array are found.
     *
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If the collection does not have any requested translations
     */
    public function mapCollection(PersistenceCollection $collection, ?array $locales = null, bool $useMainLocale = true): Collection
    {
        $locales = is_array($locales) && count($locales) > 0 ? $locales : [$collection->mainLocale];
        if ($useMainLocale && $collection->isAlwaysAvailable) {
            $locales[] = $collection->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $collection->availableLocales));
        if (count($validLocales) === 0) {
            throw new NotFoundException('collection', $collection->uuid);
        }

        $collectionData = [
            'id' => Uuid::fromString($collection->uuid),
            'blockId' => Uuid::fromString($collection->blockUuid),
            'status' => Status::from($collection->status->value),
            'offset' => $collection->offset,
            'limit' => $collection->limit,
            'items' => ItemList::fromCallable(
                fn (): array => array_map(
                    $this->mapItem(...),
                    $this->collectionHandler->loadCollectionItems($collection),
                ),
            ),
            'slots' => SlotList::fromCallable(
                fn (): array => array_map(
                    $this->mapSlot(...),
                    $this->collectionHandler->loadCollectionSlots($collection),
                ),
            ),
            'isTranslatable' => $collection->isTranslatable,
            'mainLocale' => $collection->mainLocale,
            'isAlwaysAvailable' => $collection->isAlwaysAvailable,
            'availableLocales' => $collection->availableLocales,
            'locale' => array_first($validLocales),
        ];

        return Collection::fromArray(
            $collectionData,
            [
                'query' => function () use ($collection, $locales): ?Query {
                    try {
                        $persistenceQuery = $this->collectionHandler->loadCollectionQuery($collection);
                    } catch (NotFoundException) {
                        return null;
                    }

                    return $this->mapQuery($persistenceQuery, $locales, false);
                },
            ],
        );
    }

    /**
     * Builds the API item value from persistence one.
     */
    public function mapItem(PersistenceItem $item): Item
    {
        try {
            $itemDefinition = $this->itemDefinitionRegistry->getItemDefinition($item->valueType);
        } catch (ItemDefinitionException) {
            $itemDefinition = new NullItemDefinition($item->valueType);
        }

        $itemData = [
            'id' => Uuid::fromString($item->uuid),
            'status' => Status::from($item->status->value),
            'definition' => $itemDefinition,
            'collectionId' => Uuid::fromString($item->collectionUuid),
            'position' => $item->position,
            'value' => $item->value,
            'viewType' => $item->viewType,
            'configs' => new ConfigList(
                [
                    ...$this->configMapper->mapConfig(
                        $item->config,
                        $itemDefinition->configDefinitions,
                    ),
                ],
            ),
        ];

        return Item::fromArray(
            $itemData,
            [
                'cmsItem' => function () use ($item, $itemDefinition): CmsItemInterface {
                    $valueType = $itemDefinition instanceof NullItemDefinition ?
                        'null' :
                        $itemDefinition->valueType;

                    if ($item->value === null) {
                        return new NullCmsItem($valueType);
                    }

                    return $this->cmsItemLoader->load($item->value, $valueType);
                },
            ],
        );
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
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If the query does not have any requested locales
     */
    public function mapQuery(PersistenceQuery $query, ?array $locales = null, bool $useMainLocale = true): Query
    {
        try {
            $queryType = $this->queryTypeRegistry->getQueryType($query->type);
        } catch (QueryTypeException) {
            $queryType = new NullQueryType($query->type);
        }

        $locales = is_array($locales) && count($locales) > 0 ? $locales : [$query->mainLocale];
        if ($useMainLocale && $query->isAlwaysAvailable) {
            $locales[] = $query->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $query->availableLocales));
        if (count($validLocales) === 0) {
            throw new NotFoundException('query', $query->uuid);
        }

        /** @var string $queryLocale */
        $queryLocale = array_first($validLocales);
        $untranslatableParams = [
            ...$this->parameterMapper->extractUntranslatableParameters(
                $queryType,
                $query->parameters[$query->mainLocale],
            ),
        ];

        $queryData = [
            'id' => Uuid::fromString($query->uuid),
            'status' => Status::from($query->status->value),
            'collectionId' => Uuid::fromString($query->collectionUuid),
            'queryType' => $queryType,
            'isTranslatable' => $query->isTranslatable,
            'mainLocale' => $query->mainLocale,
            'isAlwaysAvailable' => $query->isAlwaysAvailable,
            'availableLocales' => $query->availableLocales,
            'locale' => $queryLocale,
            'parameters' => new ParameterList(
                [
                    ...$this->parameterMapper->mapParameters(
                        $queryType,
                        [...$query->parameters[$queryLocale], ...$untranslatableParams],
                    ),
                ],
            ),
        ];

        return Query::fromArray($queryData);
    }

    /**
     * Builds the API slot value from persistence one.
     */
    public function mapSlot(PersistenceSlot $slot): Slot
    {
        $slotData = [
            'id' => Uuid::fromString($slot->uuid),
            'status' => Status::from($slot->status->value),
            'collectionId' => Uuid::fromString($slot->collectionUuid),
            'position' => $slot->position,
            'viewType' => $slot->viewType,
        ];

        return Slot::fromArray($slotData);
    }
}
