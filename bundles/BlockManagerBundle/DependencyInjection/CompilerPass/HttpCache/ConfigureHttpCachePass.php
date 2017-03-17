<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureHttpCachePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.http_cache.client';

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

        $httpCacheConfig = $container->getParameter('netgen_block_manager.http_cache');
        $invalidationStrategy = $httpCacheConfig['invalidation']['default_strategy'];

        if (!isset($httpCacheConfig['invalidation']['strategies'][$invalidationStrategy])) {
            throw new RuntimeException(
                sprintf(
                    'Invalidation strategy "%s" does not exist in Netgen Block Manager configuration.',
                    $invalidationStrategy
                )
            );
        }

        $strategyConfig = $httpCacheConfig['invalidation']['strategies'][$invalidationStrategy];

        $this->configureBlockCache($container, $strategyConfig);
        $this->configureLayoutCache($container, $strategyConfig);

        if (!$httpCacheConfig['invalidation']['enabled']) {
            $container->setAlias(self::SERVICE_NAME, 'netgen_block_manager.http_cache.client.null');
        }
    }

    protected function configureBlockCache(ContainerBuilder $container, array $strategyConfig)
    {
        $tagger = $strategyConfig['block']['tagger'];
        $invalidator = $strategyConfig['block']['invalidator'];

        $container->setAlias('netgen_block_manager.http_cache.block.tagger', $tagger);
        $container->setAlias('netgen_block_manager.http_cache.block.invalidator', $invalidator);
    }

    protected function configureLayoutCache(ContainerBuilder $container, array $strategyConfig)
    {
        $tagger = $strategyConfig['layout']['tagger'];
        $invalidator = $strategyConfig['layout']['invalidator'];

        $container->setAlias('netgen_block_manager.http_cache.layout.tagger', $tagger);
        $container->setAlias('netgen_block_manager.http_cache.layout.invalidator', $invalidator);
    }
}
