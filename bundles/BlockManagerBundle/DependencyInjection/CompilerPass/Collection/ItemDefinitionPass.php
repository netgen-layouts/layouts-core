<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ItemDefinitionPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.collection.registry.item_definition';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $itemDefinitionRegistry = $container->findDefinition(self::$serviceName);

        $valueTypes = $container->getParameter('netgen_block_manager.items')['value_types'];
        foreach ($valueTypes as $valueType => $valueTypeConfig) {
            $itemDefinitionServiceName = sprintf('netgen_block_manager.collection.item_definition.%s', $valueType);
            $itemDefinitionService = new Definition(ItemDefinition::class);

            $itemDefinitionService->setLazy(true);
            $itemDefinitionService->setPublic(true);
            $itemDefinitionService->addArgument($valueType);
            $itemDefinitionService->addArgument(
                array(
                    'visibility' => new Reference('netgen_block_manager.collection.item.config_definition.handler.visibility'),
                )
            );

            $itemDefinitionService->setFactory(
                array(
                    new Reference('netgen_block_manager.collection.item_definition_factory'),
                    'buildItemDefinition',
                )
            );

            $container->setDefinition($itemDefinitionServiceName, $itemDefinitionService);

            $itemDefinitionRegistry->addMethodCall(
                'addItemDefinition',
                array(
                    $valueType,
                    new Reference($itemDefinitionServiceName),
                )
            );
        }
    }
}
