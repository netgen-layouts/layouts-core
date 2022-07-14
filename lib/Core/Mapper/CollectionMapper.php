<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\LazyCollection;
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
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\Layouts\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\Layouts\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\Layouts\Persistence\Values\Collection\Slot as PersistenceSlot;
use Ramsey\Uuid\Uuid;

use function array_intersect;
use function array_map;
use function array_unique;
use function array_values;
use function count;
use function is_array;
use function iterator_to_array;

final class CollectionMapper
{
    private CollectionHandlerInterface $collectionHandler;

    private ParameterMapper $parameterMapper;

    private ConfigMapper $configMapper;

    private ItemDefinitionRegistry $itemDefinitionRegistry;

    private QueryTypeRegistry $queryTypeRegistry;

    private CmsItemLoaderInterface $cmsItemLoader;

    public function __construct(
        CollectionHandlerInterface $collectionHandler,
        ParameterMapper $parameterMapper,
        ConfigMapper $configMapper,
        ItemDefinitionRegistry $itemDefinitionRegistry,
        QueryTypeRegistry $queryTypeRegistry,
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
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If the collection does not have any requested translations
     */
    public function mapCollection(PersistenceCollection $collection, ?array $locales = null, bool $useMainLocale = true): Collection
    {
        $locales = is_array($locales) && count($locales) > 0 ? $locales : [$collection->mainLocale];
        if ($useMainLocale && $collection->alwaysAvailable) {
            $locales[] = $collection->mainLocale;
        }

        $validLocales = array_unique(array_intersect($locales, $collection->availableLocales));
        if (count($validLocales) === 0) {
            throw new NotFoundException('collection', $collection->uuid);
        }

        $collectionData = [
            'id' => Uuid::fromString($collection->uuid),
            'blockId' => Uuid::fromString($collection->blockUuid),
            'status' => $collection->status,
            'offset' => $collection->offset,
            'limit' => $collection->limit,
            'items' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceItem $item): Item => $this->mapItem($item),
                    $this->collectionHandler->loadCollectionItems($collection),
                ),
            ),
            'query' => function () use ($collection, $locales): ?Query {
                try {
                    $persistenceQuery = $this->collectionHandler->loadCollectionQuery($collection);
                } catch (NotFoundException $e) {
                    return null;
                }

                return $this->mapQuery($persistenceQuery, $locales, false);
            },
            'slots' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceSlot $slot): Slot => $this->mapSlot($slot),
                    $this->collectionHandler->loadCollectionSlots($collection),
                ),
            ),
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
            'id' => Uuid::fromString($item->uuid),
            'status' => $item->status,
            'definition' => $itemDefinition,
            'collectionId' => Uuid::fromString($item->collectionUuid),
            'position' => $item->position,
            'value' => $item->value,
            'viewType' => $item->viewType,
            'configs' => iterator_to_array(
                $this->configMapper->mapConfig(
                    $item->config,
                    $itemDefinition->getConfigDefinitions(),
                ),
            ),
            'cmsItem' => function () use ($item, $itemDefinition): CmsItemInterface {
                $valueType = !$itemDefinition instanceof NullItemDefinition ?
                    $itemDefinition->getValueType() :
                    'null';

                if ($item->value === null) {
                    return new NullCmsItem($valueType);
                }

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
     * @param string[]|null $locales
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If the query does not have any requested locales
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
            throw new NotFoundException('query', $query->uuid);
        }

        /** @var string $queryLocale */
        $queryLocale = array_values($validLocales)[0];
        $untranslatableParams = iterator_to_array(
            $this->parameterMapper->extractUntranslatableParameters(
                $queryType,
                $query->parameters[$query->mainLocale],
            ),
        );

        $queryData = [
            'id' => Uuid::fromString($query->uuid),
            'status' => $query->status,
            'collectionId' => Uuid::fromString($query->collectionUuid),
            'queryType' => $queryType,
            'isTranslatable' => $query->isTranslatable,
            'mainLocale' => $query->mainLocale,
            'alwaysAvailable' => $query->alwaysAvailable,
            'availableLocales' => $query->availableLocales,
            'locale' => $queryLocale,
            'parameters' => iterator_to_array(
                $this->parameterMapper->mapParameters(
                    $queryType,
                    $untranslatableParams + $query->parameters[$queryLocale],
                ),
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
            'status' => $slot->status,
            'collectionId' => Uuid::fromString($slot->collectionUuid),
            'position' => $slot->position,
            'viewType' => $slot->viewType,
        ];

        return Slot::fromArray($slotData);
    }
}
