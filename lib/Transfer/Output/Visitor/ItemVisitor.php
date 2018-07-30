<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Collection item value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Item
 */
final class ItemVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Item;
    }

    public function visit($collectionItem, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Item $collectionItem */

        return [
            'id' => $collectionItem->getId(),
            'position' => $collectionItem->getPosition(),
            'value' => $collectionItem->getCmsItem()->getRemoteId(),
            'value_type' => $collectionItem->getDefinition()->getValueType(),
            'configuration' => $this->visitConfiguration($collectionItem, $subVisitor),
        ];
    }

    /**
     * Visit the given $item configuration into hash representation.
     */
    private function visitConfiguration(Item $item, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($item->getConfigs() as $config) {
            $hash[$config->getConfigKey()] = $subVisitor->visit($config);
        }

        return $hash;
    }
}
