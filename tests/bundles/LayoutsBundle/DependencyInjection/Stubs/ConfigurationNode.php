<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class ConfigurationNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        return new TreeBuilder('test')->getRootNode();
    }
}
