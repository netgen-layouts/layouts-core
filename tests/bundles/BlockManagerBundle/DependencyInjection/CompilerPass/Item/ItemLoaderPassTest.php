<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemLoaderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ItemLoaderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemLoaderPass::process
     */
    public function testProcess()
    {
        $this->setDefinition(
            'netgen_block_manager.item.item_loader',
            new Definition(null, array(null, null))
        );

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_block_manager.item.value_loader', array('value_type' => 'test'));
        $this->setDefinition('netgen_block_manager.item.value_loader.test', $valueLoader);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.item.item_loader',
            1,
            array(
                'test' => new Reference('netgen_block_manager.item.value_loader.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemLoaderPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Value type must begin with a letter and be followed by any combination of letters, digits and underscore.
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag()
    {
        $this->setDefinition('netgen_block_manager.item.item_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_block_manager.item.value_loader', array('value_type' => '123'));
        $this->setDefinition('netgen_block_manager.item.value_loader.test', $valueLoader);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemLoaderPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Value loader service definition must have a 'value_type' attribute in its' tag.
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagValueType()
    {
        $this->setDefinition('netgen_block_manager.item.item_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_block_manager.item.value_loader');
        $this->setDefinition('netgen_block_manager.item.value_loader.test', $valueLoader);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ItemLoaderPass::process
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
        $container->addCompilerPass(new ItemLoaderPass());
    }
}
