<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class UrlBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlBuilderPass::process
     */
    public function testProcess()
    {
        $urlBuilder = new Definition();
        $urlBuilder->addArgument(null);

        $this->setDefinition('netgen_block_manager.item.url_builder', $urlBuilder);

        $valueUrlBuilder = new Definition();
        $valueUrlBuilder->addTag('netgen_block_manager.item.value_url_builder', array('value_type' => 'test'));
        $this->setDefinition('netgen_block_manager.item.value_url_builder.test', $valueUrlBuilder);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.item.url_builder',
            0,
            array(
                'test' => new Reference('netgen_block_manager.item.value_url_builder.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlBuilderPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Value type must begin with a letter and be followed by any combination of letters, digits and underscore.
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag()
    {
        $this->setDefinition('netgen_block_manager.item.url_builder', new Definition());

        $valueUrlBuilder = new Definition();
        $valueUrlBuilder->addTag('netgen_block_manager.item.value_url_builder', array('value_type' => '123'));
        $this->setDefinition('netgen_block_manager.item.value_url_builder.test', $valueUrlBuilder);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlBuilderPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Value URL builder service definition must have a 'value_type' attribute in its' tag.
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagValueType()
    {
        $urlBuilder = new Definition();
        $urlBuilder->addArgument(null);

        $this->setDefinition('netgen_block_manager.item.url_builder', $urlBuilder);

        $valueUrlBuilder = new Definition();
        $valueUrlBuilder->addTag('netgen_block_manager.item.value_url_builder');
        $this->setDefinition('netgen_block_manager.item.value_url_builder.test', $valueUrlBuilder);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlBuilderPass::process
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
        $container->addCompilerPass(new UrlBuilderPass());
    }
}
