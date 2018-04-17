<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ConditionTypePass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.layout.resolver.registry.condition_type';
    private static $tagName = 'netgen_block_manager.layout.resolver.condition_type';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $conditionTypeRegistry = $container->findDefinition(self::$serviceName);

        $conditionTypeServices = array_keys($container->findTaggedServiceIds(self::$tagName));
        foreach ($conditionTypeServices as $conditionTypeService) {
            $conditionTypeRegistry->addMethodCall(
                'addConditionType',
                [new Reference($conditionTypeService)]
            );
        }
    }
}
