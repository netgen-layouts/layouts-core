<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerDebugBundle\DependencyInjection;

use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class NetgenBlockManagerDebugExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        if (!in_array(WebProfilerBundle::class, $container->getParameter('kernel.bundles'), true)) {
            return;
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');
    }
}
