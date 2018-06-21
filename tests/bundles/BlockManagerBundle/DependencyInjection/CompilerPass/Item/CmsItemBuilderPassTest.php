<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\CmsItemBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class CmsItemBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\CmsItemBuilderPass::process
     */
    public function testProcess(): void
    {
        $cmsItemBuilder = new Definition();
        $cmsItemBuilder->addArgument([]);
        $cmsItemBuilder->addArgument([]);
        $this->setDefinition('netgen_block_manager.item.item_builder', $cmsItemBuilder);

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
            [
                new Reference('netgen_block_manager.item.value_converter.test'),
                new Reference('netgen_block_manager.item.value_converter.test2'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\CmsItemBuilderPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CmsItemBuilderPass());
    }
}
