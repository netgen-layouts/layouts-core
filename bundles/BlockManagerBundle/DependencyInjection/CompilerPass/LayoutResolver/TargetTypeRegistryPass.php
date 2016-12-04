<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TargetTypeRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout.resolver.registry.target_type';
    const TAG_NAME = 'netgen_block_manager.layout.resolver.target_type';

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

        $targetTypeRegistry = $container->findDefinition(self::SERVICE_NAME);

        $targetTypes = array();
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $targetType => $tag) {
            $priority = isset($tag[0]['priority']) ? (int) $tag[0]['priority'] : 0;
            $targetTypes[$priority][] = new Reference($targetType);
        }

        krsort($targetTypes);
        $targetTypes = array_merge(...$targetTypes);

        foreach ($targetTypes as $targetType) {
            $targetTypeRegistry->addMethodCall(
                'addTargetType',
                array($targetType)
            );
        }
    }
}
