<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\API\Values\Collection\SlotList;
use Netgen\Layouts\Exception\RuntimeException;
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
     * @param \Netgen\Layouts\Transfer\Output\VisitorInterface|null $subVisitor
     *
     * @return mixed
     */
    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        return [
            'id' => $value->getId()->toString(),
            'offset' => $value->getOffset(),
            'limit' => $value->getLimit(),
            'is_translatable' => $value->isTranslatable(),
            'is_always_available' => $value->isAlwaysAvailable(),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'items' => iterator_to_array($this->visitItems($value->getItems(), $subVisitor)),
            'slots' => iterator_to_array($this->visitSlots($value->getSlots(), $subVisitor)),
            'query' => $this->visitQuery($value, $subVisitor),
        ];
    }

    /**
     * Visit the given collection $items into hash representation.
     */
    private function visitItems(ItemList $items, VisitorInterface $subVisitor): Generator
    {
        foreach ($items as $item) {
            yield $subVisitor->visit($item);
        }
    }

    /**
     * Visit the given collection $slots into hash representation.
     */
    private function visitSlots(SlotList $slots, VisitorInterface $subVisitor): Generator
    {
        foreach ($slots as $slot) {
            yield $subVisitor->visit($slot);
        }
    }

    /**
     * Visit the given $collection query into hash representation.
     */
    private function visitQuery(Collection $collection, VisitorInterface $subVisitor): ?array
    {
        if (!$collection->hasQuery()) {
            return null;
        }

        return $subVisitor->visit($collection->getQuery());
    }
}
