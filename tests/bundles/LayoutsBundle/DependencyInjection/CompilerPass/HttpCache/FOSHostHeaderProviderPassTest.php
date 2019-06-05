<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use FOS\HttpCache\ProxyClient\HttpDispatcher;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\FOSHostHeaderProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class FOSHostHeaderProviderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\FOSHostHeaderProviderPass::process
     */
    public function testProcessWithParameter(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.varnish.host_header_provider.fos', new Definition(null, [null]));
        $this->setParameter('fos_http_cache.proxy_client.varnish.servers', ['http://localhost:4242', 'http://localhost:2424']);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.varnish.host_header_provider.fos',
            0,
            ['http://localhost:4242', 'http://localhost:2424']
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\FOSHostHeaderProviderPass::process
     */
    public function testProcessWithHttpDispatcher(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.varnish.host_header_provider.fos', new Definition(null, [null]));
        $this->setDefinition(HttpDispatcher::class, new Definition(null, [['http://localhost:4242', 'http://localhost:2424']]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.varnish.host_header_provider.fos',
            0,
            ['http://localhost:4242', 'http://localhost:2424']
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\FOSHostHeaderProviderPass::process
     */
    public function testProcessWithoutSupportedServers(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.varnish.host_header_provider.fos', new Definition(null, [null]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.varnish.host_header_provider.fos',
            0
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\FOSHostHeaderProviderPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FOSHostHeaderProviderPass());
    }
}
