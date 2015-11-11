<?php

namespace Netgen\Bundle\BlockManagerBundle;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\ViewTemplateProviderRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockDefinitionRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\ViewBuilderPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NetgenBlockManagerBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ViewTemplateProviderRegistryPass());
        $container->addCompilerPass(new BlockDefinitionRegistryPass());
        $container->addCompilerPass(new ViewBuilderPass());
    }
}
