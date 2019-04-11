<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection;

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

    public function postProcessConfiguration(array $config): array
    {
        return $config;
    }

    public function appendConfigurationFiles(): array
    {
        return [];
    }

    /**
     * Returns available configuration nodes for the bundle.
     *
     * @return \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface[]
     */
    protected function getConfigurationNodes(): array
    {
        return [];
    }
}
