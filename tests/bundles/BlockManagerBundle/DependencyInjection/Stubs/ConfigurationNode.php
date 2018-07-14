<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class ConfigurationNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        return (new TreeBuilder('test'))->getRootNode();
    }
}
