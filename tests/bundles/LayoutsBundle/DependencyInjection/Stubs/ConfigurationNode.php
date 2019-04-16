<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class ConfigurationNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        return (new TreeBuilder('test'))->getRootNode();
    }
}
