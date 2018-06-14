<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

abstract class ExtensionPlugin implements ExtensionPluginInterface
{
    public function preProcessConfiguration(array $configs): array
    {
        return $configs;
    }

    public function addConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $children = $rootNode->children();

        foreach ($this->getConfigurationNodes() as $node) {
            $children->append($node->getConfigurationNode());
        }
    }

    public function getConfigurationNodes(): array
    {
        return [];
    }

    public function postProcessConfiguration(array $config): array
    {
        return $config;
    }

    public function appendConfigurationFiles(): array
    {
        return [];
    }
}
