<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConditionTypeRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout.resolver.registry.condition_type';
    const TAG_NAME = 'netgen_block_manager.layout.resolver.condition_type';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $conditionTypeRegistry = $container->findDefinition(self::SERVICE_NAME);

        $conditionTypes = array();
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $conditionType => $tag) {
            $priority = isset($tag[0]['priority']) ? (int)$tag[0]['priority'] : 0;
            $conditionTypes[$priority][] = new Reference($conditionType);
        }

        krsort($conditionTypes);
        $conditionTypes = array_merge(...$conditionTypes);

        foreach ($conditionTypes as $conditionType) {
            $conditionTypeRegistry->addMethodCall(
                'addConditionType',
                array($conditionType)
            );
        }
    }
}
