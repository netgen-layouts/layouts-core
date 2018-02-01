<?php

namespace Netgen\BlockManager\Config;

interface ConfigDefinitionAwareInterface
{
    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions();
}
