<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CacheManagerPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.http_cache.fos.cache_manager';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        if (!$container->has('fos_http_cache.proxy_client.varnish')) {
            throw new RuntimeException(
                'Netgen Block Manager requires Varnish proxy client to be activated in FOSHttpCacheBundle'
            );
        }

        $cacheManager = $container->findDefinition(self::SERVICE_NAME);
        $cacheManager->replaceArgument(0, new Reference('fos_http_cache.proxy_client.varnish'));
    }
}
