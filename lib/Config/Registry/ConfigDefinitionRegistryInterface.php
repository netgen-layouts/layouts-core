<?php

namespace Netgen\BlockManager\Config\Registry;

use Netgen\BlockManager\Config\ConfigDefinitionInterface;

interface ConfigDefinitionRegistryInterface
{
    /**
     * Adds a config definition to registry.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     */
    public function addConfigDefinition($type, ConfigDefinitionInterface $configDefinition);

    /**
     * Returns all config definitions for provided type.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions($type);
}
