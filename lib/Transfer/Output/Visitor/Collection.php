<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Collection\Collection as CollectionValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Collection value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Collection
 */
final class Collection extends Visitor
{
    public function accept($value)
    {
        return $value instanceof CollectionValue;
    }

    public function visit($collection, VisitorInterface $subVisitor = null)
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
            'items' => $this->visitItems($collection->getItems(), $subVisitor),
            'query' => $this->visitQuery($collection, $subVisitor),
        ];
    }

    /**
     * Visit the given collection $items into hash representation.
     *
     * @param array $items
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface $subVisitor
     *
     * @return array
     */
    private function visitItems(array $items, VisitorInterface $subVisitor)
    {
        $hash = [];

        foreach ($items as $item) {
            $hash[$item->getPosition()] = $subVisitor->visit($item);
        }

        ksort($hash);

        return array_values($hash);
    }

    /**
     * Visit the given $collection query into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface $subVisitor
     *
     * @return array|null
     */
    private function visitQuery(CollectionValue $collection, VisitorInterface $subVisitor)
    {
        if (!$collection->hasQuery()) {
            return null;
        }

        return $subVisitor->visit($collection->getQuery());
    }
}
