<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection;

use Generator;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use function array_keys;
use function iterator_to_array;
use function sprintf;

final class ItemDefinitionPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.collection.registry.item_definition';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        /** @var array<string, mixed[]> $valueTypes */
        $valueTypes = $container->getParameter('netgen_layouts.value_types');
        $itemDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $itemDefinitions = [];

        foreach (array_keys($valueTypes) as $valueType) {
            $itemDefinitionServiceName = sprintf('netgen_layouts.collection.item_definition.%s', $valueType);

            $itemDefinitionService = new Definition(ItemDefinition::class);
            $itemDefinitionService->setFactory([new Reference('netgen_layouts.collection.item_definition_factory'), 'buildItemDefinition']);

            $itemDefinitionService->setLazy(true);
            $itemDefinitionService->setPublic(false);
            $itemDefinitionService->addArgument($valueType);
            $itemDefinitionService->addArgument(iterator_to_array($this->getConfigHandlers($container)));

            $container->setDefinition($itemDefinitionServiceName, $itemDefinitionService);

            $itemDefinitions[$valueType] = new Reference($itemDefinitionServiceName);
        }

        $itemDefinitionRegistry->replaceArgument(0, $itemDefinitions);
    }

    /**
     * @return \Generator<string, \Symfony\Component\DependencyInjection\Reference>
     */
    private function getConfigHandlers(ContainerBuilder $container): Generator
    {
        $configHandlerServices = $container->findTaggedServiceIds('netgen_layouts.item_config_handler');
        foreach ($configHandlerServices as $configHandlerService => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['config_key'])) {
                    throw new RuntimeException(
                        "Collection item config handler definition must have an 'config_key' attribute in its' tag.",
                    );
                }

                yield $tag['config_key'] => new Reference($configHandlerService);
            }
        }
    }
}
