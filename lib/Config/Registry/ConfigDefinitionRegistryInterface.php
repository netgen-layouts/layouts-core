<?php

namespace Netgen\BlockManager\Config\Registry;

use Netgen\BlockManager\Config\ConfigDefinitionInterface;

interface ConfigDefinitionRegistryInterface
{
    /**
     * Adds a config definition to registry.
     *
     * @param string $type
     * @param string $identifier
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     */
    public function addConfigDefinition($type, $identifier, ConfigDefinitionInterface $configDefinition);

    /**
     * Returns if registry has a config definition.
     *
     * @param string $type
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigDefinition($type, $identifier);

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
    public function getConfigDefinition($type, $identifier);

    /**
     * Returns all config definitions for provided type.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions($type);
}
