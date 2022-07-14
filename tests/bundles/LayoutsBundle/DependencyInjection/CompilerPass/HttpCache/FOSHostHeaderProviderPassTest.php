<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use FOS\HttpCache\ProxyClient\HttpDispatcher;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\FOSHostHeaderProviderPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

use function class_exists;

final class FOSHostHeaderProviderPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new FOSHostHeaderProviderPass());
    }

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
            ['http://localhost:4242', 'http://localhost:2424'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\FOSHostHeaderProviderPass::process
     */
    public function testProcessWithHttpDispatcher(): void
    {
        if (!class_exists(HttpDispatcher::class)) {
            self::markTestSkipped('Test requires friendsofsymfony/http-cache-bundle 2.x to run');
        }

        $this->setDefinition('netgen_layouts.http_cache.varnish.host_header_provider.fos', new Definition(null, [null]));
        $this->setDefinition(HttpDispatcher::class, new Definition(null, [['http://localhost:4242', 'http://localhost:2424']]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.varnish.host_header_provider.fos',
            0,
            ['http://localhost:4242', 'http://localhost:2424'],
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
            0,
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
}
