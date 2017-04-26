<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BlockTypeGroupPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     */
    public function testProcess()
    {
        $this->setParameter(
            'netgen_block_manager.block_type_groups',
            array(
                'test' => array(
                    'enabled' => true,
                    'block_types' => array(),
                ),
            )
        );

        $this->setParameter('netgen_block_manager.block_types', array());

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type_group.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockTypeGroup',
            array(
                new Reference('netgen_block_manager.block.block_type_group.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     */
    public function testProcessWithNoBlockType()
    {
        $this->setParameter(
            'netgen_block_manager.block_type_groups',
            array(
                'test' => array(
                    'enabled' => true,
                    'block_types' => array('test1', 'test2'),
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'test1' => array(
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type_group.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.block.block_type_group.test',
            2,
            array(
                new Reference('netgen_block_manager.block.block_type.test1'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     */
    public function testProcessWithPopulatingCustomGroup()
    {
        $this->setParameter(
            'netgen_block_manager.block_type_groups',
            array(
                'test' => array(
                    'enabled' => true,
                    'block_types' => array('test1'),
                ),
                'custom' => array(
                    'enabled' => true,
                    'block_types' => array(),
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'test1' => array(
                    'enabled' => true,
                    'definition_identifier' => 'test',
                ),
                'test2' => array(
                    'enabled' => false,
                    'definition_identifier' => 'test',
                ),
                'test3' => array(
                    'enabled' => true,
                    'definition_identifier' => 'test',
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypeGroups = $this->container->getParameter('netgen_block_manager.block_type_groups');
        $this->assertArrayHasKey('custom', $blockTypeGroups);

        $this->assertEquals(
            array(
                'enabled' => true,
                'block_types' => array('test3'),
            ),
            $blockTypeGroups['custom']
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     */
    public function testProcessWithDisabledGroup()
    {
        $this->setParameter(
            'netgen_block_manager.block_type_groups',
            array(
                'test' => array(
                    'enabled' => false,
                    'block_types' => array(),
                ),
            )
        );

        $this->setParameter('netgen_block_manager.block_types', array());

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.block.block_type_group.test');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
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
        $container->addCompilerPass(new BlockTypeGroupPass());
    }
}
