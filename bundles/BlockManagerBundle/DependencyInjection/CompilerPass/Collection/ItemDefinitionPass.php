<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ItemDefinitionPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.collection.registry.item_definition';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        if (!$container->hasParameter('netgen_block_manager.items')) {
            // By default, no item types are registered in the system
            return;
        }

        $itemConfig = $container->getParameter('netgen_block_manager.items');
        $itemDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $itemDefinitions = [];

        foreach (array_keys($itemConfig['value_types']) as $valueType) {
            $itemDefinitionServiceName = sprintf('netgen_block_manager.collection.item_definition.%s', $valueType);

            $itemDefinitionService = new Definition(ItemDefinition::class);
            $itemDefinitionService->setFactory([new Reference('netgen_block_manager.collection.item_definition_factory'), 'buildItemDefinition']);

            $itemDefinitionService->setLazy(true);
            $itemDefinitionService->setPublic(true);
            $itemDefinitionService->addArgument($valueType);
            $itemDefinitionService->addArgument($this->getConfigHandlers($container));

            $container->setDefinition($itemDefinitionServiceName, $itemDefinitionService);

            $itemDefinitions[$valueType] = new Reference($itemDefinitionServiceName);
        }

        $itemDefinitionRegistry->replaceArgument(0, $itemDefinitions);
    }

    private function getConfigHandlers(ContainerBuilder $container): array
    {
        $configHandlers = [];

        $configHandlerServices = $container->findTaggedServiceIds('netgen_block_manager.collection.item_config_handler');
        foreach ($configHandlerServices as $configHandlerService => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['config_key'])) {
                    throw new RuntimeException(
                        "Collection item config handler definition must have an 'config_key' attribute in its' tag."
                    );
                }

                $configHandlers[$tag['config_key']] = new Reference($configHandlerService);
            }
        }

        return $configHandlers;
    }
}
