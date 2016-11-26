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
        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array('block_definition' => array('enabled' => true))
        );

        $this->setParameter('netgen_block_manager.block.block_definition.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.block.block_definition.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.block.block_definition.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.block.block_definition.class', 'definition_class');
        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $blockDefinitionHandler = new Definition();
        $blockDefinitionHandler->addTag(
            'netgen_block_manager.block.block_definition_handler',
            array('identifier' => 'block_definition')
        );

        $this->setDefinition(
            'netgen_block_manager.block.block_definition.handler.test',
            $blockDefinitionHandler
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.block.block_definition.block_definition'
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
     */
    public function testProcessWithDisabledBlockDefinition()
    {
        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array('block_definition' => array('enabled' => false))
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.block.block_definition.block_definition');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array('block_definition' => array('enabled' => true))
        );

        $this->setParameter('netgen_block_manager.block.block_definition.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.block.block_definition.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.block.block_definition.class', 'definition_class');
        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $blockDefinitionHandler = new Definition();
        $blockDefinitionHandler->addTag('netgen_block_manager.block.block_definition_handler');
        $this->setDefinition('netgen_block_manager.block.block_definition.handler.test', $blockDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoHandler()
    {
        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array('block_definition' => array('enabled' => true))
        );

        $this->setParameter('netgen_block_manager.block.block_definition.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.block.block_definition.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.block.block_definition.class', 'definition_class');

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionRegistryPass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }
}
