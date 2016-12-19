<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Configuration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ContainerTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::generateContainerTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::buildContainerTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::validateContainerTypes
     */
    public function testProcess()
    {
        $this->setParameter(
            'netgen_block_manager.container_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'definition_identifier' => 'test',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array(
                'test' => array(
                    'name' => 'Test',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.configuration.registry.container_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.container_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.container_type',
            'addContainerType',
            array(
                new Reference('netgen_block_manager.configuration.container_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::generateContainerTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::buildContainerTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::validateContainerTypes
     */
    public function testProcessWithRedefinedContainerType()
    {
        $this->setParameter(
            'netgen_block_manager.container_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'definition_identifier' => 'other',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.container_definitions',
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

        $this->setDefinition('netgen_block_manager.configuration.registry.container_type', new Definition());

        $this->compile();

        $containerTypes = $this->container->getParameter('netgen_block_manager.container_types');

        $this->assertInternalType('array', $containerTypes);
        $this->assertArrayHasKey('test', $containerTypes);

        $this->assertEquals(
            array(
                'enabled' => true,
                'definition_identifier' => 'other',
            ),
            $containerTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.container_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.container_type',
            'addContainerType',
            array(
                new Reference('netgen_block_manager.configuration.container_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::generateContainerTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::buildContainerTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::validateContainerTypes
     */
    public function testProcessWithDefaultConfigForContainerType()
    {
        $this->setParameter(
            'netgen_block_manager.container_types',
            array(
                'test' => array(),
            )
        );

        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array(
                'test' => array(
                    'name' => 'Test',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.configuration.registry.container_type', new Definition());

        $this->compile();

        $containerTypes = $this->container->getParameter('netgen_block_manager.container_types');

        $this->assertInternalType('array', $containerTypes);
        $this->assertArrayHasKey('test', $containerTypes);

        $this->assertEquals(
            array(
                'name' => 'Test',
                'enabled' => true,
                'definition_identifier' => 'test',
            ),
            $containerTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.container_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.container_type',
            'addContainerType',
            array(
                new Reference('netgen_block_manager.configuration.container_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::generateContainerTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::buildContainerTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::validateContainerTypes
     */
    public function testProcessWithNonExistingContainerType()
    {
        $this->setParameter('netgen_block_manager.container_types', array());

        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array(
                'test' => array(
                    'name' => 'Test',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.configuration.registry.container_type', new Definition());

        $this->compile();

        $containerTypes = $this->container->getParameter('netgen_block_manager.container_types');
        $this->assertArrayHasKey('test', $containerTypes);

        $this->assertEquals(
            array(
                'name' => 'Test',
                'enabled' => true,
                'definition_identifier' => 'test',
                'defaults' => array(),
            ),
            $containerTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.container_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.container_type',
            'addContainerType',
            array(
                new Reference('netgen_block_manager.configuration.container_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::generateContainerTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::buildContainerTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::validateContainerTypes
     */
    public function testProcessWithDisabledContainerType()
    {
        $this->setParameter(
            'netgen_block_manager.container_types',
            array(
                'type' => array(
                    'enabled' => false,
                    'definition_identifier' => 'container',
                ),
            )
        );

        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array(
                'container' => array(
                    'name' => 'Title',
                    'enabled' => true,
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.configuration.registry.container_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.configuration.container_type.type');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::generateContainerTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::buildContainerTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::validateContainerTypes
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoContainerDefinition()
    {
        $this->setParameter(
            'netgen_block_manager.container_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'definition_identifier' => 'container',
                ),
            )
        );

        $this->setParameter('netgen_block_manager.container_definitions', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.container_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\ContainerTypePass::process
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
        $container->addCompilerPass(new ContainerTypePass());
    }
}
