<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

class BlockDefinitionHandlerWithoutCollection extends BlockDefinitionHandler
{
    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return false;
    }
}
