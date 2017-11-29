<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Block\Placeholder as PlaceholderValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;

/**
 * Placeholder value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Block\Placeholder
 */
final class Placeholder extends Visitor
{
    public function accept($value)
    {
        return $value instanceof PlaceholderValue;
    }

    public function visit($placeholder, Visitor $subVisitor = null, array $context = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder */

        return array(
            'identifier' => $placeholder->getIdentifier(),
            'blocks' => $this->visitBlocks($placeholder, $subVisitor),
        );
    }

    /**
     * Visit the given $placeholder blocks into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
     *
     * @return array
     */
    private function visitBlocks(PlaceholderValue $placeholder, Visitor $subVisitor)
    {
        $hash = array();

        foreach ($placeholder->getBlocks() as $block) {
            $hash[] = $subVisitor->visit($block);
        }

        return $hash;
    }
}
