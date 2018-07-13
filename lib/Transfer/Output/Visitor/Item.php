<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Collection\Item as ItemValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Collection item value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Item
 */
final class Item implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof ItemValue;
    }

    public function visit($collectionItem, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Item $collectionItem */

        return [
            'id' => $collectionItem->getId(),
            'type' => $this->getTypeString($collectionItem),
            'position' => $collectionItem->getPosition(),
            'value' => $collectionItem->getCmsItem()->getRemoteId(),
            'value_type' => $collectionItem->getDefinition()->getValueType(),
            'configuration' => $this->visitConfiguration($collectionItem, $subVisitor),
        ];
    }

    /**
     * Visit the given $item configuration into hash representation.
     */
    private function visitConfiguration(ItemValue $item, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($item->getConfigs() as $config) {
            $hash[$config->getConfigKey()] = $subVisitor->visit($config);
        }

        return $hash;
    }

    /**
     * Return type string representation for the given $item.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If status is not recognized
     */
    private function getTypeString(ItemValue $item): string
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
