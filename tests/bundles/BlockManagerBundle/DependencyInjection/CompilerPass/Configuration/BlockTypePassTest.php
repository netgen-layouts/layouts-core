<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Configuration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BlockTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::validateBlockTypes
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

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockType',
            array(
                new Reference('netgen_block_manager.configuration.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::validateBlockTypes
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

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

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

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockType',
            array(
                new Reference('netgen_block_manager.configuration.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::validateBlockTypes
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

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

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

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockType',
            array(
                new Reference('netgen_block_manager.configuration.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::validateBlockTypes
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

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

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

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.block_type',
            'addBlockType',
            array(
                new Reference('netgen_block_manager.configuration.block_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::validateBlockTypes
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

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.configuration.block_type.type');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::validateBlockTypes
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
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

        $this->setDefinition('netgen_block_manager.configuration.registry.block_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\BlockTypePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
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
