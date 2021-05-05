<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ItemDefinitionPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ItemDefinitionPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::getConfigHandlers
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_layouts.value_types',
            [
                'value_type' => [
                    'enabled' => true,
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.collection.item_definition_factory', new Definition());
        $this->setDefinition('netgen_layouts.collection.registry.item_definition', new Definition(null, [[]]));

        $configHandler = new Definition();
        $configHandler->addTag('netgen_layouts.item_config_handler', ['config_key' => 'key']);

        $this->setDefinition('netgen_layouts.collection.item_config_handler.key', $configHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_layouts.collection.item_definition.value_type',
            ItemDefinition::class,
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.collection.item_definition.value_type',
            1,
            [
                'key' => new Reference('netgen_layouts.collection.item_config_handler.key'),
            ],
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.collection.registry.item_definition',
            0,
            [
                'value_type' => new Reference('netgen_layouts.collection.item_definition.value_type'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::getConfigHandlers
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     */
    public function testProcessThrowsExceptionWithNoConfigKeyInTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Collection item config handler definition must have an \'config_key\' attribute in its\' tag.');

        $this->setParameter(
            'netgen_layouts.value_types',
            [
                'value_type' => [
                    'enabled' => true,
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.collection.registry.item_definition', new Definition(null, [[]]));

        $configHandler = new Definition();
        $configHandler->addTag('netgen_layouts.item_config_handler');

        $this->setDefinition('netgen_layouts.collection.item_config_handler.key', $configHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\ItemDefinitionPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
