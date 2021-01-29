<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConfigureHttpCachePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.http_cache.client';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        /** @var array<string, mixed[]> $httpCacheConfig */
        $httpCacheConfig = $container->getParameter('netgen_layouts.http_cache');

        if ($httpCacheConfig['invalidation']['enabled'] !== true) {
            $container->setAlias(self::SERVICE_NAME, 'netgen_layouts.http_cache.client.null');
        }
    }
}
