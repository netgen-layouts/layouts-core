<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Collection\Collection as CollectionValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;

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

    public function visit($collection, Visitor $subVisitor = null, array $context = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Collection $collection */

        return array(
            'id' => $collection->getId(),
            'type' => $this->getTypeString($collection),
            'status' => $this->getStatusString($collection),
            'offset' => $collection->getOffset(),
            'limit' => $collection->getLimit(),
            'is_translatable' => $collection->isTranslatable(),
            'is_always_available' => $collection->isAlwaysAvailable(),
            'main_locale' => $collection->getMainLocale(),
            'available_locales' => $collection->getAvailableLocales(),
            'manual_items' => $this->visitItems($collection->getManualItems(), $subVisitor),
            'override_items' => $this->visitItems($collection->getOverrideItems(), $subVisitor),
            'query' => $this->visitQuery($collection, $subVisitor),
        );
    }

    /**
     * Return type string representation for the given $collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If status is not recognized
     *
     * @return string
     */
    private function getTypeString(CollectionValue $collection)
    {
        switch ($collection->getType()) {
            case CollectionValue::TYPE_MANUAL:
                return 'MANUAL';
            case CollectionValue::TYPE_DYNAMIC:
                return 'DYNAMIC';
        }

        $typeString = var_export($collection->getType(), true);

        throw new RuntimeException(sprintf("Unknown type '%s'", $typeString));
    }

    /**
     * Visit the given collection $items into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item[] $items
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
     *
     * @return array
     */
    private function visitItems(array $items, Visitor $subVisitor)
    {
        $hash = array();

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
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
     *
     * @return mixed
     */
    private function visitQuery(CollectionValue $collection, Visitor $subVisitor)
    {
        if (!$collection->hasQuery()) {
            return null;
        }

        return $subVisitor->visit($collection->getQuery());
    }
}
