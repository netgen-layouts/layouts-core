<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionRegistryPass;
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionRegistryPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.configuration.block_definition.block_definition', new Definition());
        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $blockDefinition = new Definition();
        $blockDefinition->addTag('netgen_block_manager.block.block_definition', array('identifier' => 'block_definition'));
        $this->setDefinition('netgen_block_manager.block.block_definition.test', $blockDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_definition',
            'addBlockDefinition',
            array(
                new Reference('netgen_block_manager.block.block_definition.test'),
            )
        );
    }
}
