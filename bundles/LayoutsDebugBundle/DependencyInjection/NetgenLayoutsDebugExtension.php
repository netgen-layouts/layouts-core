<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class NetgenLayoutsDebugExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        if (!$this->debugEnabled($container)) {
            return;
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
    }

    private function debugEnabled(ContainerBuilder $container): bool
    {
        return array_key_exists('WebProfilerBundle', $container->getParameter('kernel.bundles'));
    }
}
