<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class VisibilityResolverPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.collection.item_visibility_resolver';
    private const TAG_NAME = 'netgen_block_manager.collection.item_visibility_resolver.voter';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $visibilityResolver = $container->findDefinition(self::SERVICE_NAME);
        $voterServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $voters = [];
        foreach ($voterServices as $serviceName => $tag) {
            $priority = (int) ($tag[0]['priority'] ?? 0);
            $voters[$priority][] = new Reference($serviceName);
        }

        if (count($voters) > 0) {
            krsort($voters);
            $voters = array_merge(...$voters);
        }

        $visibilityResolver->addMethodCall('setVoters', [$voters]);
    }
}
