<?php

namespace Netgen\Bundle\BlockManagerBundle;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;
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

        $container->addCompilerPass(new CompilerPass\Block\BlockDefinitionRegistryPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\LayoutResolverPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\DoctrineTargetHandlerPass());
        $container->addCompilerPass(new CompilerPass\View\TemplateResolverPass());
        $container->addCompilerPass(new CompilerPass\View\ViewBuilderPass());
        $container->addCompilerPass(new CompilerPass\Parameters\FormMapperPass());
        $container->addCompilerPass(new CompilerPass\Collection\QueryTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\Value\ValueLoaderRegistryPass());
        $container->addCompilerPass(new CompilerPass\Value\ValueBuilderPass());
        $container->addCompilerPass(new CompilerPass\Configuration\SourceRegistryPass());
        $container->addCompilerPass(new CompilerPass\Configuration\LayoutTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\Configuration\BlockTypeRegistryPass());
    }
}
