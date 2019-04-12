<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class DoctrineTargetHandlerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
     */
    public function testProcess(): void
    {
        $layoutResolverHandler = new Definition();
        $layoutResolverHandler->addArgument([]);
        $layoutResolverHandler->addArgument([]);
        $layoutResolverHandler->addArgument([]);

        $this->setDefinition('netgen_layouts.persistence.doctrine.layout_resolver.query_handler', $layoutResolverHandler);

        $targetHandler = new Definition();
        $targetHandler->addTag(
            'netgen_layouts.target_type.doctrine_handler',
            [
                'target_type' => 'test',
            ]
        );
        $this->setDefinition('netgen_layouts.layout.resolver.target_handler.doctrine.test', $targetHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.persistence.doctrine.layout_resolver.query_handler',
            2,
            [
                'test' => new Reference('netgen_layouts.layout.resolver.target_handler.doctrine.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DoctrineTargetHandlerPass());
    }
}
