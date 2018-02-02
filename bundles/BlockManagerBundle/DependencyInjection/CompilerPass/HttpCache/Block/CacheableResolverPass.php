<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CacheableResolverPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.http_cache.block.cacheable_resolver';
    const TAG_NAME = 'netgen_block_manager.http_cache.block.cacheable_resolver.voter';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $cacheableResolver = $container->findDefinition(self::SERVICE_NAME);
        $voterServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $voters = array();
        foreach ($voterServices as $serviceName => $tag) {
            $priority = isset($tag[0]['priority']) ? (int) $tag[0]['priority'] : 0;
            $voters[$priority][] = new Reference($serviceName);
        }

        krsort($voters);
        $voters = array_merge(...$voters);

        $cacheableResolver->addMethodCall('setVoters', array($voters));
    }
}
