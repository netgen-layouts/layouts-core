<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class VisibilityResolverPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private const SERVICE_NAME = 'netgen_block_manager.collection.item_visibility_resolver';
    private const TAG_NAME = 'netgen_block_manager.collection.item_visibility_resolver.voter';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $visibilityResolver = $container->findDefinition(self::SERVICE_NAME);
        $voters = $this->findAndSortTaggedServices(self::TAG_NAME, $container);

        $visibilityResolver->addMethodCall('setVoters', [$voters]);
    }
}
