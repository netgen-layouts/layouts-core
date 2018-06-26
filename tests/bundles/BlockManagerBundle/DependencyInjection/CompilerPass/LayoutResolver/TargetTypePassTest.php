<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class TargetTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetTypePass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_block_manager.layout.resolver.registry.target_type', new Definition());

        $targetType1 = new Definition();
        $targetType1->addTag('netgen_block_manager.layout.resolver.target_type');
        $this->setDefinition('netgen_block_manager.layout.resolver.target_type.test1', $targetType1);

        $targetType2 = new Definition();
        $targetType2->addTag('netgen_block_manager.layout.resolver.target_type');
        $this->setDefinition('netgen_block_manager.layout.resolver.target_type.test2', $targetType2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.registry.target_type',
            0,
            new Reference('netgen_block_manager.layout.resolver.target_type.test1')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.registry.target_type',
            1,
            new Reference('netgen_block_manager.layout.resolver.target_type.test2')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TargetTypePass());
    }
}
