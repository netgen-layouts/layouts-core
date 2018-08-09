<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Generator;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\ItemList;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Collection value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Collection
 */
final class CollectionVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Collection;
    }

    public function visit($collection, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Collection $collection */

        return [
            'id' => $collection->getId(),
            'offset' => $collection->getOffset(),
            'limit' => $collection->getLimit(),
            'is_translatable' => $collection->isTranslatable(),
            'is_always_available' => $collection->isAlwaysAvailable(),
            'main_locale' => $collection->getMainLocale(),
            'available_locales' => $collection->getAvailableLocales(),
            'items' => iterator_to_array($this->visitItems($collection->getItems(), $subVisitor)),
            'query' => $this->visitQuery($collection, $subVisitor),
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
