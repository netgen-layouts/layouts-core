<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Generator;
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

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /** @var \Netgen\BlockManager\API\Values\Collection\Item $value */

        return [
            'id' => $value->getId(),
            'position' => $value->getPosition(),
            'value' => $value->getCmsItem()->getRemoteId(),
            'value_type' => $value->getDefinition()->getValueType(),
            'configuration' => iterator_to_array($this->visitConfiguration($value, $subVisitor)),
        ];
    }

    /**
     * Visit the given $item configuration into hash representation.
     */
    private function visitConfiguration(Item $item, VisitorInterface $subVisitor): Generator
    {
        foreach ($item->getConfigs() as $config) {
            yield $config->getConfigKey() => $subVisitor->visit($config);
        }
    }
}
