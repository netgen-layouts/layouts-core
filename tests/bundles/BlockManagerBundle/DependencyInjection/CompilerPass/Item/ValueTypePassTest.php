<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ValueTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::buildValueTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_block_manager.items',
            [
                'value_types' => [
                    'test' => [
                        'enabled' => true,
                    ],
                ],
            ]
        );

        $this->container->setDefinition('netgen_block_manager.item.registry.value_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.item.value_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.item.registry.value_type',
            'addValueType',
            [
                'test',
                new Reference('netgen_block_manager.item.value_type.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::buildValueTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     */
    public function testProcessWithNoItems(): void
    {
        $this->container->setDefinition('netgen_block_manager.item.registry.value_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.item.value_type.test');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ValueTypePass());
    }
}
