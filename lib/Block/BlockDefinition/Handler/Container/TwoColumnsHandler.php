<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler\Container;

class TwoColumnsHandler extends ContainerHandler
{
    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return array('left', 'right');
    }
}
