<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CacheManagerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (
            !$container->has('fos_http_cache.cache_manager') ||
            !$container->has('fos_http_cache.proxy_client.varnish')
        ) {
            $container->setAlias(
                'netgen_block_manager.http_cache.client',
                'netgen_block_manager.http_cache.client.null'
            );

            return;
        }

        $cacheManager = clone $container->findDefinition('fos_http_cache.cache_manager');
        $cacheManager->replaceArgument(0, new Reference('fos_http_cache.proxy_client.varnish'));

        $container->setDefinition(
            'netgen_block_manager.http_cache.fos.cache_manager',
            $cacheManager
        );
    }
}
