<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Removes unneeded and unused config from the container.
 *
 * As a rule of thumb, this config is used only by compiler passes to bootstrap
 * various parts of the system. We remove them to try and reduce a bit size of the container.
 */
final class CleanupConfigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->getParameterBag()->remove('netgen_block_manager.items');
        $container->getParameterBag()->remove('netgen_block_manager.query_types');
        $container->getParameterBag()->remove('netgen_block_manager.block_definitions');
        $container->getParameterBag()->remove('netgen_block_manager.layout_types');
        $container->getParameterBag()->remove('netgen_block_manager.block_types');
        $container->getParameterBag()->remove('netgen_block_manager.block_type_groups');
        $container->getParameterBag()->remove('netgen_block_manager.design_list');
        $container->getParameterBag()->remove('netgen_block_manager.http_cache');
        $container->getParameterBag()->remove('netgen_block_manager.default_view_templates');
    }
}
