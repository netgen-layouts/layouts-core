<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

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
        $targetBuilders = $container->findTaggedServiceIds(self::TAG_NAME);

        $flattenedTargetBuilders = array();
        foreach ($targetBuilders as $targetBuilder => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new RuntimeException('Target builder service tags should have an "alias" attribute.');
            }

            $priority = isset($tag[0]['priority']) ? $tag[0]['priority'] : 0;
            $flattenedTargetBuilders[$priority][] = array(
                'service' => $targetBuilder,
                'alias' => $tag[0]['alias'],
            );
        }

        krsort($flattenedTargetBuilders);
        $flattenedTargetBuilders = call_user_func_array('array_merge', $flattenedTargetBuilders);

        foreach ($flattenedTargetBuilders as $targetBuilder) {
            $targetBuildersBuilderRegistry->addMethodCall(
                'addTargetBuilder',
                array($targetBuilder['alias'], new Reference($targetBuilder['service']))
            );
        }
    }
}
