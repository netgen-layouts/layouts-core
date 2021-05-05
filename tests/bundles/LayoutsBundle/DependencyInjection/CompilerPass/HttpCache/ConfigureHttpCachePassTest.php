<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass;
use stdClass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class ConfigureHttpCachePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ConfigureHttpCachePass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.client', new Definition(stdClass::class));
        $this->setParameter('session.storage.options', []);

        $this->setParameter(
            'netgen_layouts.http_cache',
            [
                'invalidation' => [
                    'enabled' => true,
                ],
            ],
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_layouts.http_cache.client',
            stdClass::class,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     */
    public function testProcessWithDisabledInvalidation(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.client', new Definition(stdClass::class));
        $this->setParameter('session.storage.options', []);

        $this->setParameter(
            'netgen_layouts.http_cache',
            [
                'invalidation' => [
                    'enabled' => false,
                ],
            ],
        );

        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'netgen_layouts.http_cache.client',
            'netgen_layouts.http_cache.client.null',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
