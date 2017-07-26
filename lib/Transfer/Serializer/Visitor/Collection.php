<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Values\Collection\Collection as CollectionValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;
use RuntimeException;

/**
 * Collection value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Collection
 */
class Collection extends Visitor
{
    public function accept($value)
    {
        return $value instanceof CollectionValue;
    }

    public function visit($collection, Visitor $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Collection $collection */

        return array(
            'id' => $collection->getId(),
            'type' => $this->getTypeString($collection),
            'status' => $this->getStatusString($collection),
            'is_published' => $collection->isPublished(),
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
     * @throws \RuntimeException If status is not recognized
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

        throw new RuntimeException("Unknown type '{$typeString}'");
    }

    /**
     * Visit the given collection $items into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item[] $items
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
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
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
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
