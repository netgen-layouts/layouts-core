<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Collection\Item as ItemValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;

/**
 * Item value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Item
 */
final class Item extends Visitor
{
    public function accept($value)
    {
        return $value instanceof ItemValue;
    }

    public function visit($item, Visitor $subVisitor = null, array $context = null)
    {
        /* @var \Netgen\BlockManager\API\Values\Collection\Item $item */

        return array(
            'id' => $item->getId(),
            'type' => $this->getTypeString($item),
            'position' => $item->getPosition(),
            'value_id' => $item->getValueId(),
            'value_type' => $item->getValueType(),
        );
    }

    /**
     * Return type string representation for the given $item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If status is not recognized
     *
     * @return string
     */
    private function getTypeString(ItemValue $item)
    {
        switch ($item->getType()) {
            case ItemValue::TYPE_MANUAL:
                return 'MANUAL';
            case ItemValue::TYPE_OVERRIDE:
                return 'OVERRIDE';
        }

        $typeString = var_export($item->getType(), true);

        throw new RuntimeException(sprintf("Unknown type '%s'", $typeString));
    }
}
