<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class FragmentRendererPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.view.renderer.fragment';
    const TAG_NAME = 'netgen_block_manager.view.fragment_view_renderer';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $fragmentRenderer = $container->findDefinition(self::SERVICE_NAME);
        $viewRendererServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $viewRenderers = array();
        foreach ($viewRendererServices as $serviceName => $tag) {
            $viewRenderers[] = new Reference($serviceName);
        }

        $fragmentRenderer->replaceArgument(3, $viewRenderers);
    }
}
