<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Configuration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LayoutTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LayoutTypePass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::validateLayoutTypes
     */
    public function testProcess()
    {
        $this->setParameter('netgen_block_manager.block_definitions', array());
        $this->setParameter(
            'netgen_block_manager.layout_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'zones' => array(),
                ),
            )
        );

        $this->container->setDefinition('netgen_block_manager.configuration.registry.layout_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.layout_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.layout_type',
            'addLayoutType',
            array(
                new Reference('netgen_block_manager.configuration.layout_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::validateLayoutTypes
     */
    public function testProcessWithDisabledLayout()
    {
        $this->setParameter('netgen_block_manager.block_definitions', array());
        $this->setParameter(
            'netgen_block_manager.layout_types',
            array(
                'test' => array(
                    'enabled' => false,
                    'zones' => array(),
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.configuration.registry.layout_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.configuration.layout_type.test');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::validateLayoutTypes
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition()
    {
        $this->setParameter('netgen_block_manager.block_definitions', array());
        $this->setParameter(
            'netgen_block_manager.layout_types',
            array(
                'test' => array(
                    'enabled' => true,
                    'zones' => array(
                        'zone' => array(
                            'allowed_block_definitions' => array('title'),
                        ),
                    ),
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.configuration.registry.layout_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypePass::process
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
