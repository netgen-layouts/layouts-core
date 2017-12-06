<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Collection\Item as ItemValue;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Transfer\Output\Visitor;

/**
 * Item value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Item
 */
final class Item extends Visitor
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(ItemLoaderInterface $itemLoader)
    {
        $this->itemLoader = $itemLoader;
    }

    public function accept($value)
    {
        return $value instanceof ItemValue;
    }

    public function visit($collectionItem, Visitor $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\Collection\Item $collectionItem */

        $valueId = null;

        try {
            $item = $this->itemLoader->load(
                $collectionItem->getValueId(),
                $collectionItem->getValueType()
            );

            $valueId = $item->getRemoteId();
        } catch (ItemException $e) {
            // Do nothing
        }

        return array(
            'id' => $collectionItem->getId(),
            'type' => $this->getTypeString($collectionItem),
            'position' => $collectionItem->getPosition(),
            'value_id' => $valueId,
            'value_type' => $collectionItem->getValueType(),
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
