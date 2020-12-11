<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\HttpCache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Updates the tagger definition to use the tag handler from HTTP Cache Bundle 1.x, if installed.
 *
 * @deprecated Remove when support for HTTP Cache Bundle 1.x ends.
 */
final class LegacyTagHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (
            !$container->has('netgen_layouts.http_cache.tagger')
            || !$container->has('fos_http_cache.handler.tag_handler')
        ) {
            return;
        }

        $tagger = $container->findDefinition('netgen_layouts.http_cache.tagger');
        $tagger->replaceArgument(0, new Reference('fos_http_cache.handler.tag_handler'));
    }
}
