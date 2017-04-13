<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

abstract class DynamicContainerHandler extends ContainerDefinitionHandler
{
    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return array();
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return true;
    }
}
