<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ItemDefinitionPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.collection.registry.item_definition';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        if (!$container->hasParameter('netgen_block_manager.items')) {
            // By default, no item types are registered in the system
            return;
        }

        $itemConfig = $container->getParameter('netgen_block_manager.items');
        $itemDefinitionRegistry = $container->findDefinition(self::$serviceName);

        foreach (array_keys($itemConfig['value_types']) as $valueType) {
            $itemDefinitionServiceName = sprintf('netgen_block_manager.collection.item_definition.%s', $valueType);
            $itemDefinitionService = new Definition(ItemDefinition::class);

            $itemDefinitionService->setLazy(true);
            $itemDefinitionService->setPublic(true);
            $itemDefinitionService->addArgument($valueType);
            $itemDefinitionService->addArgument(
                [
                    'visibility' => new Reference('netgen_block_manager.collection.item.config_definition.handler.visibility'),
                ]
            );

            $itemDefinitionService->setFactory(
                [
                    new Reference('netgen_block_manager.collection.item_definition_factory'),
                    'buildItemDefinition',
                ]
            );

            $container->setDefinition($itemDefinitionServiceName, $itemDefinitionService);

            $itemDefinitionRegistry->addMethodCall(
                'addItemDefinition',
                [
                    $valueType,
                    new Reference($itemDefinitionServiceName),
                ]
            );
        }
    }
}
