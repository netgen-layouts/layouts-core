<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Configuration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypeRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LayoutTypeRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LayoutTypeRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypeRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypeRegistryPass::validateLayoutTypes
     */
    public function testProcess()
    {
        $this->setParameter('netgen_block_manager.layout_types', array());
        $this->setParameter('netgen_block_manager.block_definitions', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.layout_type', new Definition());

        $layoutType = new Definition();
        $layoutType->addTag('netgen_block_manager.configuration.layout_type');
        $this->setDefinition('netgen_block_manager.configuration.layout_type.test', $layoutType);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.layout_type',
            'addLayoutType',
            array(
                new Reference('netgen_block_manager.configuration.layout_type.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypeRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\LayoutTypeRegistryPass::validateLayoutTypes
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition()
    {
        $this->setParameter(
            'netgen_block_manager.layout_types',
            array(
                'type' => array(
                    'zones' => array(
                        'zone' => array(
                            'allowed_block_definitions' => array('title'),
                        ),
                    ),
                ),
            )
        );

        $this->setParameter('netgen_block_manager.block_definitions', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.layout_type', new Definition());

        $layoutType = new Definition();
        $layoutType->addTag('netgen_block_manager.configuration.layout_type');
        $this->setDefinition('netgen_block_manager.configuration.layout_type.test', $layoutType);

        $this->compile();
    }
}
