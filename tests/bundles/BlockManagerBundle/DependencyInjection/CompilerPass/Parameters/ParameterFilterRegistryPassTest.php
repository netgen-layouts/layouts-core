<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Parameters;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParameterFilterRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ParameterFilterRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParameterFilterRegistryPass::process
     */
    public function testProcess()
    {
        $parameterFilterRegistry = new Definition();
        $parameterFilterRegistry->addArgument(array());
        $this->setDefinition('netgen_block_manager.parameters.registry.parameter_filter', $parameterFilterRegistry);

        $filter1 = new Definition();
        $filter1->addTag('netgen_block_manager.parameters.parameter_filter', array('type' => 'html'));
        $this->setDefinition('netgen_block_manager.parameters.parameter_filter.test1', $filter1);

        $filter2 = new Definition();
        $filter2->addTag('netgen_block_manager.parameters.parameter_filter', array('priority' => 5, 'type' => 'html'));
        $this->setDefinition('netgen_block_manager.parameters.parameter_filter.test2', $filter2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.parameters.registry.parameter_filter',
            'addParameterFilters',
            array(
                'html',
                array(
                    new Reference('netgen_block_manager.parameters.parameter_filter.test2'),
                    new Reference('netgen_block_manager.parameters.parameter_filter.test1'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParameterFilterRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Parameter filter service definition must have a 'type' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTypeIdentifier()
    {
        $this->setDefinition('netgen_block_manager.parameters.registry.parameter_filter', new Definition());

        $filter = new Definition();
        $filter->addTag('netgen_block_manager.parameters.parameter_filter');
        $this->setDefinition('netgen_block_manager.parameters.parameter_filter.test', $filter);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParameterFilterRegistryPass::process
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
        $container->addCompilerPass(new ParameterFilterRegistryPass());
    }
}
