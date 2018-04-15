<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Layout;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class LayoutTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::validateLayoutTypes
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

        $this->container->setDefinition('netgen_block_manager.layout.registry.layout_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.layout.layout_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.layout.registry.layout_type',
            'addLayoutType',
            array(
                'test',
                new Reference('netgen_block_manager.layout.layout_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::validateLayoutTypes
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Block definition "title" used in "test" layout type does not exist.
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

        $this->setDefinition('netgen_block_manager.layout.registry.layout_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
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
        $container->addCompilerPass(new LayoutTypePass());
    }
}
