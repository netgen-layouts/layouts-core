<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\LegacyTagHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class LegacyTagHandlerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\LegacyTagHandlerPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.tagger', new Definition(null, [null]));
        $this->setDefinition('fos_http_cache.handler.tag_handler', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.tagger',
            0,
            new Reference('fos_http_cache.handler.tag_handler')
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\LegacyTagHandlerPass::process
     */
    public function testProcessWithoutTagger(): void
    {
        $this->setDefinition('fos_http_cache.handler.tag_handler', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_layouts.http_cache.tagger');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\LegacyTagHandlerPass::process
     */
    public function testProcessWithoutTagHandler(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.tagger', new Definition(null, [null]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.tagger',
            0
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache\LegacyTagHandlerPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LegacyTagHandlerPass());
    }
}
