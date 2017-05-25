<?php

namespace Netgen\BlockManager\Config\Registry;

use Netgen\BlockManager\Config\ConfigDefinitionInterface;

class ConfigDefinitionRegistry implements ConfigDefinitionRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface[][]
     */
    protected $configDefinitions = array();

    /**
     * Adds a config definition to registry.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     */
    public function addConfigDefinition($type, ConfigDefinitionInterface $configDefinition)
    {
        $this->configDefinitions[$type][] = $configDefinition;
    }

    /**
     * Returns all config definitions for provided type.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions($type)
    {
        if (!isset($this->configDefinitions[$type])) {
            return array();
        }

        return $this->configDefinitions[$type];
    }
}
