<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TargetTypePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.layout.resolver.registry.target_type';
    private const TAG_NAME = 'netgen_block_manager.layout.resolver.target_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $targetTypeRegistry = $container->findDefinition(self::SERVICE_NAME);

        $targetTypes = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $targetType => $tag) {
            $priority = (int) ($tag[0]['priority'] ?? 0);
            $targetTypes[$priority][] = new Reference($targetType);
        }

        krsort($targetTypes);
        $targetTypes = array_merge(...$targetTypes);

        foreach ($targetTypes as $targetType) {
            $targetTypeRegistry->addMethodCall(
                'addTargetType',
                [$targetType]
            );
        }
    }
}
