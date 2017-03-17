<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CacheManagerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass::process
     */
    public function testProcess()
    {
        $cacheManager = new Definition();
        $cacheManager->addArgument(null);
        $this->setDefinition('netgen_block_manager.http_cache.fos.cache_manager', $cacheManager);

        $proxyClient = new Definition();
        $this->setDefinition('fos_http_cache.proxy_client.varnish', $proxyClient);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.http_cache.fos.cache_manager',
            0,
            new Reference('fos_http_cache.proxy_client.varnish')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Netgen Block Manager requires Varnish proxy client to be activated in FOSHttpCacheBundle
     */
    public function testProcessWithoutVarnishProxyClient()
    {
        $cacheManager = new Definition();
        $cacheManager->addArgument(null);
        $this->setDefinition('netgen_block_manager.http_cache.fos.cache_manager', $cacheManager);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\CacheManagerPass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CacheManagerPass());
    }
}
