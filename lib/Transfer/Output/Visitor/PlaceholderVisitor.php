<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Placeholder value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Block\Placeholder
 */
final class PlaceholderVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Placeholder;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Block\Placeholder $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return array
     */
    public function visit($value, AggregateVisitor $aggregateVisitor): array
    {
        return [
            'identifier' => $value->getIdentifier(),
            'blocks' => iterator_to_array($this->visitBlocks($value, $aggregateVisitor)),
        ];
    }

    /**
     * Visit the given $placeholder blocks into hash representation.
     */
    private function visitBlocks(Placeholder $placeholder, AggregateVisitor $aggregateVisitor): Generator
    {
        foreach ($placeholder as $block) {
            yield $aggregateVisitor->visit($block);
        }
    }
}
