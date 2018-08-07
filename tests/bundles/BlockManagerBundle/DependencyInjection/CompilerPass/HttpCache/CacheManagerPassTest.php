<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class CacheManagerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass::process
     */
    public function testProcess(): void
    {
        $cacheManager = new Definition(null, [0]);
        $this->setDefinition('fos_http_cache.cache_manager', $cacheManager);
        $this->setDefinition('fos_http_cache.proxy_client.varnish', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.http_cache.fos.cache_manager',
            0,
            new Reference('fos_http_cache.proxy_client.varnish')
        );

        $def = $this->container->findDefinition('netgen_block_manager.http_cache.fos.cache_manager');
        self::assertFalse($def->isPublic());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass::process
     */
    public function testProcessWithoutCacheManager(): void
    {
        $this->setDefinition('fos_http_cache.proxy_client.varnish', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.http_cache.client',
            'netgen_block_manager.http_cache.client.null'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass::process
     */
    public function testProcessWithoutVarnishProxyClient(): void
    {
        $this->setDefinition('fos_http_cache.cache_manager', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.http_cache.client',
            'netgen_block_manager.http_cache.client.null'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CacheManagerPass());
    }
}
