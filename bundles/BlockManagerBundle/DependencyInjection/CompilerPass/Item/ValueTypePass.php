<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Item\ValueType\ValueTypeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValueTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.item.registry.value_type';
    const TAG_NAME = 'netgen_block_manager.item.value_type';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $itemConfig = $container->getParameter('netgen_block_manager.items');
        $valueTypeServices = $this->buildValueTypes($container, $itemConfig['value_types']);

        $registry = $container->findDefinition(self::SERVICE_NAME);

        foreach ($valueTypeServices as $identifier => $valueTypeService) {
            $registry->addMethodCall(
                'addValueType',
                array($identifier, new Reference($valueTypeService))
            );
        }
    }

    /**
     * Builds the value type objects from provided configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $valueTypes
     *
     * @return array
     */
    private function buildValueTypes(ContainerBuilder $container, array $valueTypes)
    {
        $valueTypeServices = array();

        foreach ($valueTypes as $identifier => $valueType) {
            $serviceIdentifier = sprintf('netgen_block_manager.item.value_type.%s', $identifier);

            $container->register($serviceIdentifier, ValueType::class)
                ->setArguments(array($identifier, $valueType))
                ->setLazy(true)
                ->setFactory(array(ValueTypeFactory::class, 'buildValueType'));

            $valueTypeServices[$identifier] = $serviceIdentifier;
        }

        return $valueTypeServices;
    }
}
