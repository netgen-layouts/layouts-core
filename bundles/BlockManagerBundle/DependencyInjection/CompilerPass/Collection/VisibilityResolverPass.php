<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class VisibilityResolverPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.collection.item.visibility_resolver';
    const TAG_NAME = 'netgen_block_manager.collection.item.visibility_voter';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $visibilityResolver = $container->findDefinition(self::SERVICE_NAME);
        $voterServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $voters = array();
        foreach ($voterServices as $serviceName => $tag) {
            $priority = isset($tag[0]['priority']) ? (int) $tag[0]['priority'] : 0;
            $voters[$priority][] = new Reference($serviceName);
        }

        if (!empty($voters)) {
            krsort($voters);
            $voters = array_merge(...$voters);
        }

        $visibilityResolver->addMethodCall('setVoters', array($voters));
    }
}
