<?php

namespace Netgen\BlockManager\Configuration;

use InvalidArgumentException;

abstract class Configuration implements ConfigurationInterface
{
    /**
     * Returns the configuration for specified block definition.
     *
     * @param string $definitionIdentifier
     *
     * @throws \InvalidArgumentException If configuration for specified block definition does not exist
     *
     * @return array
     */
    public function getBlockDefinitionConfig($definitionIdentifier)
    {
        $blockDefinitionConfig = $this->getParameter('block_definitions');

        if (!isset($blockDefinitionConfig[$definitionIdentifier])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" block definition does not exist.',
                    $definitionIdentifier
                )
            );
        }

        return $blockDefinitionConfig[$definitionIdentifier];
    }

    /**
     * Returns the configuration for specified block type.
     *
     * @param string $identifier
     *
     * @throws \InvalidArgumentException If configuration for specified block type not exist
     *
     * @return array
     */
    public function getBlockTypeConfig($identifier)
    {
        $blockTypeConfig = $this->getParameter('block_types');

        if (!isset($blockTypeConfig[$identifier])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" block type does not exist.',
                    $identifier
                )
            );
        }

        return $blockTypeConfig[$identifier];
    }

    /**
     * Returns the configuration for specified layout.
     *
     * @param string $layoutType
     *
     * @throws \InvalidArgumentException If configuration for specified layout does not exist
     *
     * @return array
     */
    public function getLayoutConfig($layoutType)
    {
        $layoutTypes = $this->getParameter('layout_types');

        if (!isset($layoutTypes[$layoutType])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" layout does not exist.',
                    $layoutType
                )
            );
        }

        return $layoutTypes[$layoutType];
    }
}
