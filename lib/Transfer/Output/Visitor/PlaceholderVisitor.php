<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;

/**
 * Placeholder value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Block\Placeholder
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Block\Placeholder>
 */
final class PlaceholderVisitor implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return $value instanceof Placeholder;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'identifier' => $value->getIdentifier(),
            'blocks' => iterator_to_array($this->visitBlocks($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $placeholder blocks into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitBlocks(Placeholder $placeholder, OutputVisitor $outputVisitor): Generator
    {
        foreach ($placeholder as $block) {
            yield $outputVisitor->visit($block);
        }
    }
}
