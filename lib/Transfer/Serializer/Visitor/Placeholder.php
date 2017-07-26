<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Values\Block\Placeholder as PlaceholderValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;

/**
 * Placeholder value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Block\Placeholder
 */
class Placeholder extends Visitor
{
    public function accept($value)
    {
        return $value instanceof PlaceholderValue;
    }

    public function visit($placeholder, Visitor $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder */

        return array(
            'identifier' => $placeholder->getIdentifier(),
            'block_references' => $this->getBlockReferences($placeholder),
        );
    }

    /**
     * Return an array of block references for the given $placeholder.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder
     *
     * @return array
     */
    private function getBlockReferences(PlaceholderValue $placeholder)
    {
        $references = array();

        foreach ($placeholder->getBlocks() as $block) {
            $references[] = $block->getId();
        }

        return $references;
    }
}
