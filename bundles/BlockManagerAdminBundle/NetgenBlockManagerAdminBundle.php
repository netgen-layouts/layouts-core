<?php

namespace Netgen\Bundle\BlockManagerAdminBundle;

use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenBlockManagerAdminBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $configuration = new Configuration();

        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $blockManagerExtension */
        $blockManagerExtension = $container->getExtension('netgen_block_manager');

        $blockManagerExtension->addConfigTreeBuilder(
            $configuration->getConfigTreeBuilderClosure()
        );
    }
}
