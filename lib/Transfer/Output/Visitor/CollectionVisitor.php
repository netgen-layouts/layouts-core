<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\API\Values\Collection\SlotList;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;

/**
 * Collection value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Collection\Collection
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Collection\Collection>
 */
final class CollectionVisitor implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return $value instanceof Collection;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'id' => $value->getId()->toString(),
            'offset' => $value->getOffset(),
            'limit' => $value->getLimit(),
            'is_translatable' => $value->isTranslatable(),
            'is_always_available' => $value->isAlwaysAvailable(),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'items' => iterator_to_array($this->visitItems($value->getItems(), $outputVisitor)),
            'slots' => iterator_to_array($this->visitSlots($value->getSlots(), $outputVisitor)),
            'query' => $this->visitQuery($value, $outputVisitor),
        ];
    }

    /**
     * Visit the given collection $items into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitItems(ItemList $items, OutputVisitor $outputVisitor): Generator
    {
        foreach ($items as $item) {
            yield $outputVisitor->visit($item);
        }
    }

    /**
     * Visit the given collection $slots into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitSlots(SlotList $slots, OutputVisitor $outputVisitor): Generator
    {
        foreach ($slots as $slot) {
            yield $outputVisitor->visit($slot);
        }
    }

    /**
     * Visit the given $collection query into hash representation.
     *
     * @return array<string, mixed>|null
     */
    private function visitQuery(Collection $collection, OutputVisitor $outputVisitor): ?array
    {
        $query = $collection->getQuery();
        if ($query === null) {
            return null;
        }

        return $outputVisitor->visit($query);
    }
}
