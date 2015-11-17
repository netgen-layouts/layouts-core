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
