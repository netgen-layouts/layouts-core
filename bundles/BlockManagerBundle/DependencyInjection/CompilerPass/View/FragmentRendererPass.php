<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class FragmentRendererPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.view.renderer.fragment';
    private static $tagName = 'netgen_block_manager.view.fragment_view_renderer';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $fragmentRenderer = $container->findDefinition(self::$serviceName);
        $viewRendererServices = $container->findTaggedServiceIds(self::$tagName);

        $viewRenderers = [];
        foreach (array_keys($viewRendererServices) as $serviceName) {
            $viewRenderers[] = new Reference($serviceName);
        }

        $fragmentRenderer->replaceArgument(3, $viewRenderers);
    }
}
