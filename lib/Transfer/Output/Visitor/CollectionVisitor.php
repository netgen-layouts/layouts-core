<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\API\Values\Collection\SlotList;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Collection value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Collection\Collection
 */
final class CollectionVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Collection;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Collection\Collection $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return array
     */
    public function visit($value, AggregateVisitor $aggregateVisitor): array
    {
        return [
            'id' => $value->getId()->toString(),
            'offset' => $value->getOffset(),
            'limit' => $value->getLimit(),
            'is_translatable' => $value->isTranslatable(),
            'is_always_available' => $value->isAlwaysAvailable(),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'items' => iterator_to_array($this->visitItems($value->getItems(), $aggregateVisitor)),
            'slots' => iterator_to_array($this->visitSlots($value->getSlots(), $aggregateVisitor)),
            'query' => $this->visitQuery($value, $aggregateVisitor),
        ];
    }

    /**
     * Visit the given collection $items into hash representation.
     */
    private function visitItems(ItemList $items, AggregateVisitor $aggregateVisitor): Generator
    {
        foreach ($items as $item) {
            yield $aggregateVisitor->visit($item);
        }
    }

    /**
     * Visit the given collection $slots into hash representation.
     */
    private function visitSlots(SlotList $slots, AggregateVisitor $aggregateVisitor): Generator
    {
        foreach ($slots as $slot) {
            yield $aggregateVisitor->visit($slot);
        }
    }

    /**
     * Visit the given $collection query into hash representation.
     */
    private function visitQuery(Collection $collection, AggregateVisitor $aggregateVisitor): ?array
    {
        if (!$collection->hasQuery()) {
            return null;
        }

        return $aggregateVisitor->visit($collection->getQuery());
    }
}
