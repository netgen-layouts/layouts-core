<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

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
            'identifier' => $value->identifier,
            'blocks' => [...$this->visitBlocks($value, $outputVisitor)],
        ];
    }

    /**
     * Visit the given $placeholder blocks into hash representation.
     *
     * @return iterable<array<string, mixed>>
     */
    private function visitBlocks(Placeholder $placeholder, OutputVisitor $outputVisitor): iterable
    {
        foreach ($placeholder->blocks as $block) {
            yield $outputVisitor->visit($block);
        }
    }
}
