<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetTypeRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TargetTypeRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetTypeRegistryPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.layout.resolver.registry.target_type', new Definition());

        $targetType = new Definition();
        $targetType->addTag('netgen_block_manager.layout.resolver.target_type');
        $this->setDefinition('netgen_block_manager.layout.resolver.target_type.test', $targetType);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.layout.resolver.registry.target_type',
            'addTargetType',
            array(
                new Reference('netgen_block_manager.layout.resolver.target_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetTypeRegistryPass::process
     * @doesNotPerformAssertions
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TargetTypeRegistryPass());
    }
}
