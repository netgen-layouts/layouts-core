<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection;

use Jean85\PrettyVersions;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

final class NetgenBlockManagerAdminExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_settings.yml');

        $loader->load('services/menu.yml');
        $loader->load('services/templating.yml');
        $loader->load('services/controllers.yml');
        $loader->load('services/event_listeners.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $prependConfigs = [
            'framework/assets.yml' => 'framework',
            'framework/twig.yml' => 'twig',
            'view/form_view.yml' => 'netgen_block_manager',
            'view/layout_view.yml' => 'netgen_block_manager',
            'view/rule_target_view.yml' => 'netgen_block_manager',
            'view/rule_condition_view.yml' => 'netgen_block_manager',
            'view/default_templates.yml' => 'netgen_block_manager',
        ];

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
