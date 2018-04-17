<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

abstract class DynamicContainerHandler extends ContainerDefinitionHandler
{
    public function getPlaceholderIdentifiers()
    {
        return [];
    }

    public function isDynamicContainer()
    {
        return true;
    }
}
