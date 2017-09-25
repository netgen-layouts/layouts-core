<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ConditionTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout.resolver.registry.condition_type';
    const TAG_NAME = 'netgen_block_manager.layout.resolver.condition_type';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $conditionTypeRegistry = $container->findDefinition(self::SERVICE_NAME);

        $conditionTypeServices = array_keys($container->findTaggedServiceIds(self::TAG_NAME));
        foreach ($conditionTypeServices as $conditionTypeService) {
            $conditionTypeRegistry->addMethodCall(
                'addConditionType',
                array(new Reference($conditionTypeService))
            );
        }
    }
}
