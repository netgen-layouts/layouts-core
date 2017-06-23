<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class BlockTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcess()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'definition_identifier' => 'test',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array(
                'test' => array(
                    'name' => 'Test',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            array(
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithRedefinedBlockType()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'definition_identifier' => 'other',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array(
                'test' => array(
                    'name' => 'Test',
                    'enabled' => true,
                ),
                'other' => array(
                    'name' => 'Other',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('test', $blockTypes);

        $this->assertEquals(
            array(
                'enabled' => true,
                'definition_identifier' => 'other',
            ),
            $blockTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            array(
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDefaultConfigForBlockType()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'test' => array(),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array(
                'test' => array(
                    'name' => 'Test',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('test', $blockTypes);

        $this->assertEquals(
            array(
                'name' => 'Test',
                'enabled' => true,
                'definition_identifier' => 'test',
            ),
            $blockTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            array(
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithNonExistingBlockType()
    {
        $this->setParameter('netgen_block_manager.block_types', array());

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array(
                'test' => array(
                    'name' => 'Test',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');
        $this->assertArrayHasKey('test', $blockTypes);

        $this->assertEquals(
            array(
                'name' => 'Test',
                'enabled' => true,
                'definition_identifier' => 'test',
                'defaults' => array(),
            ),
            $blockTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            array(
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockType()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'type' => array(
                    'enabled' => false,
                    'definition_identifier' => 'title',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array(
                'title' => array(
                    'name' => 'Title',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('type', $blockTypes);

        $this->assertEquals(
            array(
                'enabled' => false,
                'definition_identifier' => 'title',
            ),
            $blockTypes['type']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.type');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            array(
                'type',
                new Reference('netgen_block_manager.block.block_type.type'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockDefinition()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'title' => array(
                    'enabled' => true,
                    'definition_identifier' => 'title',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array(
                'title' => array(
                    'name' => 'Title',
                    'enabled' => false,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('title', $blockTypes);

        $this->assertEquals(
            array(
                'enabled' => false,
                'definition_identifier' => 'title',
                'name' => 'Title',
            ),
            $blockTypes['title']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.title');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            array(
                'title',
                new Reference('netgen_block_manager.block.block_type.title'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockDefinitionAndAdditionalBlockType()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'type' => array(
                    'enabled' => true,
                    'definition_identifier' => 'title',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            array(
                'title' => array(
                    'name' => 'Title',
                    'enabled' => false,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('type', $blockTypes);

        $this->assertEquals(
            array(
                'enabled' => false,
                'definition_identifier' => 'title',
            ),
            $blockTypes['type']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.type');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            array(
                'type',
                new Reference('netgen_block_manager.block.block_type.type'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Block definition "title" used in "test" block type does not exist.
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition()
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'definition_identifier' => 'title',
                ),
            )
        );

        $this->setParameter('netgen_block_manager.block_definitions', array());

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BlockTypePass());
    }
}
