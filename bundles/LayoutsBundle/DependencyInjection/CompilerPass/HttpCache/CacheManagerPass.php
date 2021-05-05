<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CacheManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (
            !$container->has('fos_http_cache.cache_manager')
            || !$container->has('fos_http_cache.proxy_client.varnish')
        ) {
            $container->setAlias(
                'netgen_layouts.http_cache.client',
                'netgen_layouts.http_cache.client.null',
            );

            return;
        }

        $cacheManager = clone $container->findDefinition('fos_http_cache.cache_manager');
        $cacheManager->replaceArgument(0, new Reference('fos_http_cache.proxy_client.varnish'));

        $container->setDefinition(
            'netgen_layouts.http_cache.fos.cache_manager',
            $cacheManager,
        );
    }
}
