<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache;

use FOS\HttpCache\ProxyClient\HttpDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function class_exists;

final class FOSHostHeaderProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('netgen_layouts.http_cache.varnish.host_header_provider.fos')) {
            return;
        }

        $servers = [];
        if ($container->hasParameter('fos_http_cache.proxy_client.varnish.servers')) {
            $servers = $container->getParameter('fos_http_cache.proxy_client.varnish.servers');
        } elseif (class_exists(HttpDispatcher::class) && $container->has(HttpDispatcher::class)) {
            $servers = $container->findDefinition(HttpDispatcher::class)->getArgument(0);
        }

        $hostProvider = $container->findDefinition('netgen_layouts.http_cache.varnish.host_header_provider.fos');
        $hostProvider->replaceArgument(0, $servers);
    }
}
