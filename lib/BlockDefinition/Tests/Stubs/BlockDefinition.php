<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Stubs;

use Netgen\BlockManager\BlockDefinition\BlockDefinition as BaseBlockDefinition;
use Netgen\BlockManager\API\Values\Page\Block;

class BlockDefinition extends BaseBlockDefinition
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'block_definition';
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return array
     */
    public function getValues(Block $block)
    {
        return array();
    }
}
