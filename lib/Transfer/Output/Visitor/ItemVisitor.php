<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;

/**
 * Collection item value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Collection\Item
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Collection\Item>
 */
final class ItemVisitor implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return $value instanceof Item;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'id' => $value->getId()->toString(),
            'position' => $value->getPosition(),
            'value' => $value->getCmsItem()->getRemoteId(),
            'value_type' => $value->getDefinition()->getValueType(),
            'view_type' => $value->getViewType(),
            'configuration' => iterator_to_array($this->visitConfiguration($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $item configuration into hash representation.
     *
     * @return \Generator<string, mixed>
     */
    private function visitConfiguration(Item $item, OutputVisitor $outputVisitor): Generator
    {
        foreach ($item->getConfigs() as $config) {
            yield $config->getConfigKey() => $outputVisitor->visit($config);
        }
    }
}
