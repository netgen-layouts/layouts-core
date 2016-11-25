<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueLoaderRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ValueLoaderRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ValueLoaderRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueLoaderRegistryPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.item.registry.value_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_block_manager.item.value_loader', array('value_type' => 'test'));
        $this->setDefinition('netgen_block_manager.item.value_loader.test', $valueLoader);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.item.registry.value_loader',
            'addValueLoader',
            array(
                new Reference('netgen_block_manager.item.value_loader.test'),
            )
        );

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.item.value_types',
            array('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueLoaderRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag()
    {
        $this->setDefinition('netgen_block_manager.item.registry.value_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_block_manager.item.value_loader', array('value_type' => '123'));
        $this->setDefinition('netgen_block_manager.item.value_loader.test', $valueLoader);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueLoaderRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagValueType()
    {
        $this->setDefinition('netgen_block_manager.item.registry.value_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_block_manager.item.value_loader');
        $this->setDefinition('netgen_block_manager.item.value_loader.test', $valueLoader);

        $this->compile();
    }
}
