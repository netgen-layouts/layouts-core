<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition;
use Netgen\BlockManager\Configuration\BlockDefinition\ViewType;

class BlockDefinitionFactory
{
    /**
     * Builds the block definition configuration.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition
     */
    public static function buildBlockDefinition(array $config, $identifier)
    {
        $viewTypes = array();

        foreach ($config['view_types'] as $viewTypeIdentifier => $viewTypeConfig) {
            $viewTypes[$viewTypeIdentifier] = new ViewType(
                $viewTypeIdentifier,
                $config['view_types'][$viewTypeIdentifier]['name']
            );
        }

        return new BlockDefinition($identifier, $config['forms'], $viewTypes);
    }
}
