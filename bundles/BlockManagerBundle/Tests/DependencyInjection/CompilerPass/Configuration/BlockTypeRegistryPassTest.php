<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Configuration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BlockTypeRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BlockTypeRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypeGroups
     */
    public function testProcess()
    {
        $this->setParameter('netgen_block_manager.block_types', array());
        $this->setParameter('netgen_block_manager.block_type_groups', array());
        $this->setParameter('netgen_block_manager.block_definitions', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockType = new Definition();
        $blockType->addTag('netgen_block_manager.configuration.block_type');
        $this->setDefinition('netgen_block_manager.configuration.block_type.test', $blockType);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockType',
            array(
                new Reference('netgen_block_manager.configuration.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypeGroups
     */
    public function testProcessGroup()
    {
        $this->setParameter('netgen_block_manager.block_types', array());
        $this->setParameter('netgen_block_manager.block_type_groups', array());
        $this->setParameter('netgen_block_manager.block_definitions', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockTypeGroup = new Definition();
        $blockTypeGroup->addTag('netgen_block_manager.configuration.block_type_group');
        $this->setDefinition('netgen_block_manager.configuration.block_type_group.test', $blockTypeGroup);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockTypeGroup',
            array(
                new Reference('netgen_block_manager.configuration.block_type_group.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypeGroups
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'type' => array(
                    'definition_identifier' => 'title',
                ),
            )
        );

        $this->setParameter('netgen_block_manager.block_type_groups', array());
        $this->setParameter('netgen_block_manager.block_definitions', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockType = new Definition();
        $blockType->addTag('netgen_block_manager.configuration.block_type');
        $this->setDefinition('netgen_block_manager.configuration.block_type.test', $blockType);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::validateBlockTypeGroups
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockType()
    {
        $this->setParameter(
            'netgen_block_manager.block_type_groups',
            array(
                'group' => array(
                    'block_types' => array('title'),
                ),
            )
        );

        $this->setParameter('netgen_block_manager.block_types', array());
        $this->setParameter('netgen_block_manager.block_definitions', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockType = new Definition();
        $blockType->addTag('netgen_block_manager.configuration.block_type');
        $this->setDefinition('netgen_block_manager.configuration.block_type.test', $blockType);

        $this->compile();
    }
}
