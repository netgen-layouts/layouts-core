<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConfigureHttpCachePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.http_cache.client';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $httpCacheConfig = $container->getParameter('netgen_block_manager.http_cache');

        if (!$httpCacheConfig['invalidation']['enabled']) {
            $container->setAlias(self::SERVICE_NAME, 'netgen_block_manager.http_cache.client.null');
        }
    }
}
