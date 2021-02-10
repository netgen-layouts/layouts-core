<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\StructBuilder;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\API\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\API\Values\Collection\SlotUpdateStruct;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;

final class CollectionStructBuilder
{
    private ConfigStructBuilder $configStructBuilder;

    public function __construct(ConfigStructBuilder $configStructBuilder)
    {
        $this->configStructBuilder = $configStructBuilder;
    }

    /**
     * Creates a new collection create struct.
     */
    public function newCollectionCreateStruct(?QueryCreateStruct $queryCreateStruct = null): CollectionCreateStruct
    {
        $struct = new CollectionCreateStruct();
        $struct->queryCreateStruct = $queryCreateStruct;

        return $struct;
    }

    /**
     * Creates a new collection update struct.
     *
     * If collection is provided, initial data is copied from the collection.
     */
    public function newCollectionUpdateStruct(?Collection $collection = null): CollectionUpdateStruct
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        if ($collection !== null) {
            $collectionUpdateStruct->offset = $collection->getOffset();
            $collectionUpdateStruct->limit = $collection->getLimit() ?? 0;
        }

        return $collectionUpdateStruct;
    }

    /**
     * Creates a new item create struct from provided values.
     *
     * @param int|string $value
     */
    public function newItemCreateStruct(ItemDefinitionInterface $itemDefinition, $value): ItemCreateStruct
    {
        $struct = new ItemCreateStruct();
        $struct->definition = $itemDefinition;
        $struct->value = $value;

        return $struct;
    }

    /**
     * Creates a new item update struct.
     *
     * If item is provided, initial data is copied from the item.
     */
    public function newItemUpdateStruct(?Item $item = null): ItemUpdateStruct
    {
        $itemUpdateStruct = new ItemUpdateStruct();

        if (!$item instanceof Item) {
            return $itemUpdateStruct;
        }

        $itemUpdateStruct->viewType = $item->getViewType() ?? '';

        $this->configStructBuilder->buildConfigUpdateStructs($item, $itemUpdateStruct);

        return $itemUpdateStruct;
    }

    /**
     * Creates a new query create struct from provided query type.
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType): QueryCreateStruct
    {
        return new QueryCreateStruct($queryType);
    }

    /**
     * Creates a new query update struct for provided locale.
     *
     * If query is provided, initial data is copied from the query.
     */
    public function newQueryUpdateStruct(string $locale, ?Query $query = null): QueryUpdateStruct
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->locale = $locale;

        if (!$query instanceof Query) {
            return $queryUpdateStruct;
        }

        $queryUpdateStruct->fillParametersFromQuery($query);

        return $queryUpdateStruct;
    }

    /**
     * Creates a new slot create struct.
     */
    public function newSlotCreateStruct(): SlotCreateStruct
    {
        return new SlotCreateStruct();
    }

    /**
     * Creates a new slot update struct.
     *
     * If slot is provided, initial data is copied from the slot.
     */
    public function newSlotUpdateStruct(?Slot $slot = null): SlotUpdateStruct
    {
        $slotUpdateStruct = new SlotUpdateStruct();

        if (!$slot instanceof Slot) {
            return $slotUpdateStruct;
        }

        $slotUpdateStruct->viewType = $slot->getViewType();

        return $slotUpdateStruct;
    }
}
