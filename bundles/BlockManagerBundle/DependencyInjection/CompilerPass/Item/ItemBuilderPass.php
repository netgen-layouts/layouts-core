<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ItemBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.item.item_builder';
    const TAG_NAME = 'netgen_block_manager.item.value_converter';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $itemBuilder = $container->findDefinition(self::SERVICE_NAME);
        $valueConverterServices = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        $valueConverters = array();
        foreach ($valueConverterServices as $serviceName) {
            $valueConverters[] = new Reference($serviceName);
        }

        $itemBuilder->replaceArgument(0, $valueConverters);
    }
}
