<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CacheableResolverPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.http_cache.block.cacheable_resolver';
    const TAG_NAME = 'netgen_block_manager.http_cache.block.cacheable_resolver.voter';

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

        $cacheableResolver = $container->findDefinition(self::SERVICE_NAME);
        $voterServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $voters = array();
        foreach ($voterServices as $serviceName => $tag) {
            $voters[] = new Reference($serviceName);
        }

        $cacheableResolver->replaceArgument(0, $voters);
    }
}
