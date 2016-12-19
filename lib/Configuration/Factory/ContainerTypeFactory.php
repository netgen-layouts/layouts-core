<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\ContainerType\ContainerType;
use Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface;

class ContainerTypeFactory
{
    /**
     * Builds the container type.
     *
     * @param string $identifier
     * @param array $config
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface $containerDefinition
     *
     * @return \Netgen\BlockManager\Configuration\ContainerType\ContainerType
     */
    public static function buildContainerType($identifier, array $config, ContainerDefinitionInterface $containerDefinition)
    {
        return new ContainerType(
            array(
                'identifier' => $identifier,
                'name' => $config['name'],
                'containerDefinition' => $containerDefinition,
                'defaults' => $config['defaults'],
            )
        );
    }
}
