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

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Collection $value */

        return [
            'id' => $value->getId(),
            'offset' => $value->getOffset(),
            'limit' => $value->getLimit(),
            'is_translatable' => $value->isTranslatable(),
            'is_always_available' => $value->isAlwaysAvailable(),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'items' => iterator_to_array($this->visitItems($value->getItems(), $subVisitor)),
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
