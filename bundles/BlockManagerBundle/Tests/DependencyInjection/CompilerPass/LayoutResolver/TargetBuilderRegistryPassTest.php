<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetBuilderRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TargetBuilderRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TargetBuilderRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetBuilderRegistryPass::process
     */
    public function testProcess()
    {
        $targetBuilderRegistry = new Definition();
        $this->setDefinition('netgen_block_manager.layout_resolver.target_builder.registry', $targetBuilderRegistry);

        $targetBuilder1 = new Definition();
        $targetBuilder1->addTag(
            'netgen_block_manager.layout_resolver.target_builder',
            array(
                'alias' => 'test1',
                'priority' => 10
            )
        );
        $this->setDefinition('netgen_block_manager.layout_resolver.target_builder.test1', $targetBuilder1);

        $targetBuilder2 = new Definition();
        $targetBuilder2->addTag(
            'netgen_block_manager.layout_resolver.target_builder',
            array(
                'alias' => 'test2',
                'priority' => 20
            )
        );
        $this->setDefinition('netgen_block_manager.layout_resolver.target_builder.test2', $targetBuilder2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.layout_resolver.target_builder.registry',
            'addTargetBuilder',
            array(
                'test2', new Reference('netgen_block_manager.layout_resolver.target_builder.test2'),
            )
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.layout_resolver.target_builder.registry',
            'addTargetBuilder',
            array(
                'test1', new Reference('netgen_block_manager.layout_resolver.target_builder.test1'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetBuilderRegistryPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWhenNoAlias()
    {
        $targetBuilderRegistry = new Definition();
        $this->setDefinition('netgen_block_manager.layout_resolver.target_builder.registry', $targetBuilderRegistry);

        $targetBuilder = new Definition();
        $targetBuilder->addTag('netgen_block_manager.layout_resolver.target_builder');
        $this->setDefinition('netgen_block_manager.layout_resolver.target_builder.test', $targetBuilder);

        $this->compile();
    }
}
