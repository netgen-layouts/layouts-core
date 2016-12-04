<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ItemBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemBuilderPass::process
     */
    public function testProcess()
    {
        $itemBuilder = new Definition();
        $itemBuilder->addArgument(array());
        $itemBuilder->addArgument(array());
        $this->setDefinition('netgen_block_manager.item.item_builder', $itemBuilder);

        $valueConverter = new Definition();
        $valueConverter->addTag('netgen_block_manager.item.value_converter');
        $this->setDefinition('netgen_block_manager.item.value_converter.test', $valueConverter);

        $valueConverter2 = new Definition();
        $valueConverter2->addTag('netgen_block_manager.item.value_converter');
        $this->setDefinition('netgen_block_manager.item.value_converter.test2', $valueConverter2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.item.item_builder',
            0,
            array(
                new Reference('netgen_block_manager.item.value_converter.test'),
                new Reference('netgen_block_manager.item.value_converter.test2'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemBuilderPass::process
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
        $container->addCompilerPass(new ItemBuilderPass());
    }
}
