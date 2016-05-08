<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\BlockDefinition;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockDefinitionRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BlockDefinitionRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BlockDefinitionRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockDefinitionRegistryPass::process
     */
    public function testProcess()
    {
        $blockDefinitionRegistry = new Definition();
        $this->setDefinition('netgen_block_manager.block_definition.registry', $blockDefinitionRegistry);

        $blockDefinition = new Definition();
        $blockDefinition->addTag('netgen_block_manager.block_definition');
        $this->setDefinition('netgen_block_manager.block_definition.test', $blockDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block_definition.registry',
            'addBlockDefinition',
            array(
                new Reference('netgen_block_manager.block_definition.test'),
            )
        );
    }
}
