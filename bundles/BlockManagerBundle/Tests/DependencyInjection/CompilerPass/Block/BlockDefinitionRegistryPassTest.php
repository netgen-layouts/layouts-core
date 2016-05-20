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
        $blockDefinitions = array('block_definition' => array('config'));
        $this->setParameter('netgen_block_manager.block_definitions', $blockDefinitions);
        $this->setParameter('netgen_block_manager.block.block_definition.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.block.block_definition.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.block.block_definition.class', 'definition_class');

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $blockDefinitionHandler = new Definition();
        $blockDefinitionHandler->addTag('netgen_block_manager.block.block_definition_handler', array('identifier' => 'block_definition'));
        $this->setDefinition('netgen_block_manager.block.block_definition.handler.test', $blockDefinitionHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.block.block_definition.configuration.block_definition',
            'config_class'
        );

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.block.block_definition.block_definition',
            'definition_class'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.block.block_definition.block_definition',
            0,
            'block_definition'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.block.block_definition.block_definition',
            1,
            new Reference('netgen_block_manager.block.block_definition.handler.test')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.block.block_definition.block_definition',
            2,
            new Reference('netgen_block_manager.block.block_definition.configuration.block_definition')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_definition',
            'addBlockDefinition',
            array(
                new Reference('netgen_block_manager.block.block_definition.block_definition'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionRegistryPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $blockDefinitions = array('block_definition' => array('config'));
        $this->setParameter('netgen_block_manager.block_definitions', $blockDefinitions);
        $this->setParameter('netgen_block_manager.block.block_definition.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.block.block_definition.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.block.block_definition.class', 'definition_class');

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $blockDefinitionHandler = new Definition();
        $blockDefinitionHandler->addTag('netgen_block_manager.block.block_definition_handler');
        $this->setDefinition('netgen_block_manager.block.block_definition.handler.test', $blockDefinitionHandler);

        $this->compile();
    }
}
