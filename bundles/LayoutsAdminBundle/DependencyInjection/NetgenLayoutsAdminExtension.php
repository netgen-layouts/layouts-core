<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

use function file_get_contents;

final class NetgenLayoutsAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');

        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new GlobFileLoader($container, $locator),
                    new YamlFileLoader($container, $locator),
                ],
            ),
        );

        $loader->load('default_settings.yaml');
        $loader->load('services/**/*.yaml', 'glob');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $prependConfigs = [
            'framework/assets.yaml' => 'framework',
            'framework/twig.yaml' => 'twig',
            'view/form_view.yaml' => 'netgen_layouts',
            'view/item_view.yaml' => 'netgen_layouts',
            'view/block_view.yaml' => 'netgen_layouts',
            'view/layout_view.yaml' => 'netgen_layouts',
            'view/rule_target_view.yaml' => 'netgen_layouts',
            'view/rule_condition_view.yaml' => 'netgen_layouts',
            'view/default_templates.yaml' => 'netgen_layouts',
        ];

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
