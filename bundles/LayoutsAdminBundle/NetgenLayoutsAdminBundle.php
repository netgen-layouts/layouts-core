<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle;

use Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ExtensionPlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenLayoutsAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $layoutsExtension */
        $layoutsExtension = $container->getExtension('netgen_block_manager');
        $layoutsExtension->addPlugin(new ExtensionPlugin());
    }
}
