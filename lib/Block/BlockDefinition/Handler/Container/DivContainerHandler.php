<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler\Container;

class DivContainerHandler extends ContainerHandler
{
    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return array('main');
    }
}
