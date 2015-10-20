<?php

namespace Netgen\BlockManager\BlockDefinition;

abstract class BlockDefinition
{
    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters()
    {
        return array(
            new Parameters\Text('css_id', 'CSS ID'),
            new Parameters\Text('css_class', 'CSS class'),
        );
    }
}
