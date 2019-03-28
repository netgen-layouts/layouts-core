<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ViewBuilderPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.view.view_builder';
    private const TAG_NAME = 'netgen_block_manager.view.provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $viewBuilder = $container->findDefinition(self::SERVICE_NAME);
        $viewProviderServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $viewProviders = [];
        foreach ($viewProviderServices as $serviceName => $tag) {
            $priority = (int) ($tag[0]['priority'] ?? 0);
            $viewProviders[$priority][] = new Reference($serviceName);
        }

        if (count($viewProviders) > 0) {
            krsort($viewProviders);
            $viewProviders = array_merge(...$viewProviders);
        }

        $viewBuilder->replaceArgument(2, $viewProviders);
    }
}
