<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Value;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Value\ValueBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ValueBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ValueBuilderPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Value\ValueBuilderPass::process
     */
    public function testProcess()
    {
        $valueBuilder = new Definition();
        $valueBuilder->addArgument(array());
        $valueBuilder->addArgument(array());
        $this->setDefinition('netgen_block_manager.value.value_builder', $valueBuilder);

        $valueConverter = new Definition();
        $valueConverter->addTag('netgen_block_manager.value.value_converter');
        $this->setDefinition('netgen_block_manager.value.value_converter.test', $valueConverter);

        $valueConverter2 = new Definition();
        $valueConverter2->addTag('netgen_block_manager.value.value_converter');
        $this->setDefinition('netgen_block_manager.value.value_converter.test2', $valueConverter2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.value.value_builder',
            1,
            array(
                new Reference('netgen_block_manager.value.value_converter.test'),
                new Reference('netgen_block_manager.value.value_converter.test2'),
            )
        );
    }
}
