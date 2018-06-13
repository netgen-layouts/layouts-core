<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ItemDefinitionPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     */
    public function testProcess()
    {
        $this->setParameter(
            'netgen_block_manager.items',
            [
                'value_types' => [
                    'value_type' => [
                        'enabled' => true,
                    ],
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.collection.registry.item_definition', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.item_definition.value_type',
            ItemDefinition::class
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.registry.item_definition',
            'addItemDefinition',
            [
                'value_type',
                new Reference('netgen_block_manager.collection.item_definition.value_type'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     */
    public function testProcessWithoutItemsConfig()
    {
        $this->setDefinition('netgen_block_manager.collection.registry.item_definition', new Definition());

        $this->compile();

        $registry = $this->container->getDefinition('netgen_block_manager.collection.registry.item_definition');

        $this->assertEmpty($registry->getMethodCalls());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
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
        $container->addCompilerPass(new ItemDefinitionPass());
    }
}
