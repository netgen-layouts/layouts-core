<?php

namespace Netgen\BlockManager\Configuration;

use RuntimeException;

abstract class Configuration implements ConfigurationInterface
{
    /**
     * Returns the configuration for specified block.
     *
     * @param string $definitionIdentifier
     *
     * @return array
     */
    public function getBlockConfig($definitionIdentifier)
    {
        $blockConfig = $this->getParameter('blocks');

        if (!isset($blockConfig[$definitionIdentifier])) {
            throw new RuntimeException(
                sprintf(
                    'Configuration for "%s" block definition does not exist.',
                    $definitionIdentifier
                )
            );
        }

        return $blockConfig[$definitionIdentifier];
    }

    /**
     * Returns the configuration for specified block type.
     *
     * @param string $identifier
     *
     * @return array
     */
    public function getBlockTypeConfig($identifier)
    {
        $blockTypeConfig = $this->getParameter('block_types');

        if (!isset($blockTypeConfig[$identifier])) {
            throw new RuntimeException(
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
     * @param string $layoutIdentifier
     *
     * @return array
     */
    public function getLayoutConfig($layoutIdentifier)
    {
        $layoutConfig = $this->getParameter('layouts');

        if (!isset($layoutConfig[$layoutIdentifier])) {
            throw new RuntimeException(
                sprintf(
                    'Configuration for "%s" layout does not exist.',
                    $layoutIdentifier
                )
            );
        }

        return $layoutConfig[$layoutIdentifier];
    }
}
