<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Exception\RuntimeException;
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
     * @param \Netgen\Layouts\Transfer\Output\VisitorInterface|null $subVisitor
     *
     * @return mixed
     */
    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        return [
            'identifier' => $value->getIdentifier(),
            'blocks' => iterator_to_array($this->visitBlocks($value, $subVisitor)),
        ];
    }

    /**
     * Visit the given $placeholder blocks into hash representation.
     */
    private function visitBlocks(Placeholder $placeholder, VisitorInterface $subVisitor): Generator
    {
        foreach ($placeholder as $block) {
            yield $subVisitor->visit($block);
        }
    }
}
