<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CmsItemBuilderPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.item.item_builder';
    private static $tagName = 'netgen_block_manager.item.value_converter';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $cmsItemBuilder = $container->findDefinition(self::$serviceName);
        $valueConverterServices = array_keys($container->findTaggedServiceIds(self::$tagName));

        $valueConverters = [];
        foreach ($valueConverterServices as $serviceName) {
            $valueConverters[] = new Reference($serviceName);
        }

        $cmsItemBuilder->replaceArgument(0, $valueConverters);
    }
}
