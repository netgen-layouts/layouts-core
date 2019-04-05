<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TargetTypePass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private const SERVICE_NAME = 'netgen_block_manager.layout.resolver.registry.target_type';
    private const TAG_NAME = 'netgen_block_manager.layout.resolver.target_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $targetTypeRegistry = $container->findDefinition(self::SERVICE_NAME);
        $targetTypes = $this->findAndSortTaggedServices(self::TAG_NAME, $container);

        foreach ($targetTypes as $targetType) {
            $targetTypeRegistry->addArgument($targetType);
        }
    }
}
