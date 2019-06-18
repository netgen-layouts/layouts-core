<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Collection item value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Collection\Item
 */
final class ItemVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Item;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Collection\Item $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return array
     */
    public function visit($value, AggregateVisitor $aggregateVisitor): array
    {
        return [
            'id' => $value->getId()->toString(),
            'position' => $value->getPosition(),
            'value' => $value->getCmsItem()->getRemoteId(),
            'value_type' => $value->getDefinition()->getValueType(),
            'view_type' => $value->getViewType(),
            'configuration' => iterator_to_array($this->visitConfiguration($value, $aggregateVisitor)),
        ];
    }

    /**
     * Visit the given $item configuration into hash representation.
     */
    private function visitConfiguration(Item $item, AggregateVisitor $aggregateVisitor): Generator
    {
        foreach ($item->getConfigs() as $config) {
            yield $config->getConfigKey() => $aggregateVisitor->visit($config);
        }
    }
}
