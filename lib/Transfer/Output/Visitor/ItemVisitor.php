<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

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
            'id' => $value->id->toString(),
            'position' => $value->position,
            'value' => $value->cmsItem->remoteId,
            'value_type' => $value->definition->valueType,
            'view_type' => $value->viewType,
            'configuration' => [...$this->visitConfiguration($value, $outputVisitor)],
        ];
    }

    /**
     * Visit the given $item configuration into hash representation.
     *
     * @return iterable<string, mixed>
     */
    private function visitConfiguration(Item $item, OutputVisitor $outputVisitor): iterable
    {
        foreach ($item->configs as $config) {
            yield $config->configKey => $outputVisitor->visit($config);
        }
    }
}
