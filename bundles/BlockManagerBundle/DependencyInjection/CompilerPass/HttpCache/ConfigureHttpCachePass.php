<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

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

        if (Kernel::VERSION_ID >= 30400) {
            // On Symfony 3.4 and up, Symfony forces max-age=0, private, must-revalidate
            // Cache-Control headers which make the AJAX and ESI block caching useless,
            // so we disable it here. Ref: https://github.com/symfony/symfony/issues/25448
            $sessionStorageOptions = $container->getParameter('session.storage.options');
            $sessionStorageOptions['cache_limiter'] = '0';
            $container->setParameter('session.storage.options', $sessionStorageOptions);
        }
    }
}
