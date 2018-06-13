<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

interface ConfigurationNodeInterface
{
    /**
     * Returns a node definition.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode();
}
