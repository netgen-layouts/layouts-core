<?php

namespace Netgen\BlockManager\Config\Registry;

use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Exception\Config\ConfigDefinitionException;

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
     * @param string $identifier
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     */
    public function addConfigDefinition($type, $identifier, ConfigDefinitionInterface $configDefinition)
    {
        $this->configDefinitions[$type][$identifier] = $configDefinition;
    }

    /**
     * Returns if registry has a config definition.
     *
     * @param string $type
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigDefinition($type, $identifier)
    {
        return isset($this->configDefinitions[$type]) &&
            isset($this->configDefinitions[$type][$identifier]);
    }

    /**
     * Returns a config definition with provided type and identifier.
     *
     * @param string $type
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Config\ConfigDefinitionException If config definition does not exist
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function getConfigDefinition($type, $identifier)
    {
        if (!$this->hasConfigDefinition($type, $identifier)) {
            throw ConfigDefinitionException::noConfigDefinition($type, $identifier);
        }

        return $this->configDefinitions[$type][$identifier];
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
