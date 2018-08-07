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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::getConfigHandlers
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_block_manager.value_types',
            [
                'value_type' => [
                    'enabled' => true,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.collection.registry.item_definition', new Definition(null, [[]]));

        $configHandler = new Definition();
        $configHandler->addTag('netgen_block_manager.collection.item_config_handler', ['config_key' => 'key']);

        $this->setDefinition('netgen_block_manager.collection.item_config_handler.key', $configHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.item_definition.value_type',
            ItemDefinition::class
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.collection.item_definition.value_type',
            1,
            [
                'key' => new Reference('netgen_block_manager.collection.item_config_handler.key'),
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.collection.registry.item_definition',
            0,
            [
                'value_type' => new Reference('netgen_block_manager.collection.item_definition.value_type'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::getConfigHandlers
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Collection item config handler definition must have an 'config_key' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoConfigKeyInTag(): void
    {
        $this->setParameter(
            'netgen_block_manager.value_types',
            [
                'value_type' => [
                    'enabled' => true,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.collection.registry.item_definition', new Definition(null, [[]]));

        $configHandler = new Definition();
        $configHandler->addTag('netgen_block_manager.collection.item_config_handler');

        $this->setDefinition('netgen_block_manager.collection.item_config_handler.key', $configHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ItemDefinitionPass());
    }
}
