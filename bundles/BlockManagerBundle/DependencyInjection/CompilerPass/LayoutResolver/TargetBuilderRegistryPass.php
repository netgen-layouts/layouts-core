<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TargetBuilderRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout_resolver.target_builder.registry';
    const TAG_NAME = 'netgen_block_manager.layout_resolver.target_builder';

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

        $targetBuildersBuilderRegistry = $container->findDefinition(self::SERVICE_NAME);
        $targetBuildersBuilders = $container->findTaggedServiceIds(self::TAG_NAME);

        $flattenedTargetBuilders = array();
        foreach ($targetBuildersBuilders as $targetBuilders => $tag) {
            $flattenedTargetBuilders[$targetBuilders] = isset($tag[0]['priority']) ? $tag[0]['priority'] : 0;
        }

        arsort($flattenedTargetBuilders);

        foreach (array_keys($flattenedTargetBuilders) as $targetBuilders) {
            $targetBuildersBuilderRegistry->addMethodCall(
                'addTargetBuilder',
                array(new Reference($targetBuilders))
            );
        }
    }
}
