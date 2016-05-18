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
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockType = new Definition();
        $blockType->addTag('netgen_block_manager.configuration.block_type', array('identifier' => 'block_type'));
        $this->setDefinition('netgen_block_manager.configuration.block_type.test', $blockType);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockType',
            array(
                'block_type',
                new Reference('netgen_block_manager.configuration.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockType = new Definition();
        $blockType->addTag('netgen_block_manager.configuration.block_type');
        $this->setDefinition('netgen_block_manager.configuration.block_type.test', $blockType);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::process
     */
    public function testProcessGroup()
    {
        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockTypeGroup = new Definition();
        $blockTypeGroup->addTag('netgen_block_manager.configuration.block_type_group', array('identifier' => 'block_type_group'));
        $this->setDefinition('netgen_block_manager.configuration.block_type_group.test', $blockTypeGroup);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockTypeGroup',
            array(
                'block_type_group',
                new Reference('netgen_block_manager.configuration.block_type_group.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypeRegistryPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessGroupThrowsExceptionWithNoTagIdentifier()
    {
        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $blockTypeGroup = new Definition();
        $blockTypeGroup->addTag('netgen_block_manager.configuration.block_type_group');
        $this->setDefinition('netgen_block_manager.configuration.block_type_group.test', $blockTypeGroup);

        $this->compile();
    }
}
