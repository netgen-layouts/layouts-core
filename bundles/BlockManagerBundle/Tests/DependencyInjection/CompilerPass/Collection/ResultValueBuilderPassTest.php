<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ResultValueBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ResultValueBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ResultValueBuilderPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ResultValueBuilderPass::process
     */
    public function testProcess()
    {
        $resultValueBuilder = new Definition();
        $resultValueBuilder->addArgument(array());
        $this->setDefinition('netgen_block_manager.collection.result_value_builder', $resultValueBuilder);

        $valueConverter = new Definition();
        $valueConverter->addTag('netgen_block_manager.collection.value_converter');
        $this->setDefinition('netgen_block_manager.collection.value_converter.test', $valueConverter);

        $valueConverter2 = new Definition();
        $valueConverter2->addTag('netgen_block_manager.collection.value_converter');
        $this->setDefinition('netgen_block_manager.collection.value_converter.test2', $valueConverter2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.collection.result_value_builder',
            0,
            array(
                new Reference('netgen_block_manager.collection.value_converter.test'),
                new Reference('netgen_block_manager.collection.value_converter.test2'),
            )
        );
    }
}
