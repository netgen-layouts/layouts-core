<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Values\Collection\Item as ItemValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;
use RuntimeException;

/**
 * Item value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Item
 */
class Item extends Visitor
{
    public function accept($value)
    {
        return $value instanceof ItemValue;
    }

    public function visit($item, Visitor $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\Collection\Item $item */

        return array(
            'id' => $item->getId(),
            'status' => $this->getStatusString($item),
            'is_published' => $item->isPublished(),
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
     * @throws \RuntimeException If status is not recognized
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

        throw new RuntimeException("Unknown type '{$typeString}'");
    }
}
