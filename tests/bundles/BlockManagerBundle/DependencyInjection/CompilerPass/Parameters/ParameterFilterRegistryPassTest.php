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

        $matcher1 = new Definition();
        $matcher1->addTag('netgen_block_manager.parameters.parameter_filter', array('type' => 'html'));
        $this->setDefinition('netgen_block_manager.parameters.parameter_filter.test1', $matcher1);

        $matcher2 = new Definition();
        $matcher2->addTag('netgen_block_manager.parameters.parameter_filter', array('priority' => 5, 'type' => 'html'));
        $this->setDefinition('netgen_block_manager.parameters.parameter_filter.test2', $matcher2);

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

        $matcher = new Definition();
        $matcher->addTag('netgen_block_manager.parameters.parameter_filter');
        $this->setDefinition('netgen_block_manager.parameters.parameter_filter.test', $matcher);

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
