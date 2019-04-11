<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

interface ExtensionPluginInterface
{
    /**
     * Pre-processes the configuration before it is resolved.
     */
    public function preProcessConfiguration(array $configs): array;

    /**
     * Processes the configuration for the bundle.
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode): void;

    /**
     * Post-processes the resolved configuration.
     */
    public function postProcessConfiguration(array $config): array;

    /**
     * Returns the array of files to be appended to main bundle configuration.
     */
    public function appendConfigurationFiles(): array;
}
