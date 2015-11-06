<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Stubs;

use Netgen\BlockManager\BlockDefinition\BlockDefinition as BaseBlockDefinition;

class BlockDefinition extends BaseBlockDefinition
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getValues(array $parameters = array())
    {
    }
}
