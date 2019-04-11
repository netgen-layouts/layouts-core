<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

interface ConfigurationNodeInterface
{
    /**
     * Returns a node definition.
     */
    public function getConfigurationNode(): NodeDefinition;
}
