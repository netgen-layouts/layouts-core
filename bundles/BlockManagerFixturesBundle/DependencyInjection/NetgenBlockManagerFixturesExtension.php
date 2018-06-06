<?php

namespace Netgen\Bundle\BlockManagerFixturesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

final class NetgenBlockManagerFixturesExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $prependConfigs = [
            'layout_types.yml' => 'netgen_block_manager',
            'block_definitions.yml' => 'netgen_block_manager',
            'query_types.yml' => 'netgen_block_manager',
            'block_types.yml' => 'netgen_block_manager',
            'value_types.yml' => 'netgen_block_manager',
            'view/layout_view.yml' => 'netgen_block_manager',
        ];

        foreach (array_reverse($prependConfigs) as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
