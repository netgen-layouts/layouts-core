<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Item\ValueType\ValueTypeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ValueTypePass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.item.registry.value_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        if (!$container->hasParameter('netgen_block_manager.items')) {
            // By default, no value types are registered in the system
            return;
        }

        $itemConfig = $container->getParameter('netgen_block_manager.items');
        $valueTypeServices = $this->buildValueTypes($container, $itemConfig['value_types']);

        $registry = $container->findDefinition(self::$serviceName);

        foreach ($valueTypeServices as $identifier => $valueTypeService) {
            $registry->addMethodCall(
                'addValueType',
                [$identifier, new Reference($valueTypeService)]
            );
        }
    }

    /**
     * Builds the value type objects from provided configuration.
     */
    private function buildValueTypes(ContainerBuilder $container, array $valueTypes): array
    {
        $valueTypeServices = [];

        foreach ($valueTypes as $identifier => $valueType) {
            $this->validateBrowserType($container, $identifier);

            $serviceIdentifier = sprintf('netgen_block_manager.item.value_type.%s', $identifier);

            $container->register($serviceIdentifier, ValueType::class)
                ->setArguments([$identifier, $valueType])
                ->setLazy(true)
                ->setPublic(true)
                ->setFactory([ValueTypeFactory::class, 'buildValueType']);

            $valueTypeServices[$identifier] = $serviceIdentifier;
        }

        return $valueTypeServices;
    }

    /**
     * Validates that the provided Content Browser item type exists in the system.
     */
    private function validateBrowserType(ContainerBuilder $containerBuilder, string $browserType): void
    {
        $validBrowserTypes = $containerBuilder->getParameter('netgen_content_browser.item_types');

        if (is_array($validBrowserTypes) && array_key_exists($browserType, $validBrowserTypes)) {
            return;
        }

        throw new RuntimeException(
            sprintf('Content Browser backend for "%s" type does not exist.', $browserType)
        );
    }
}
