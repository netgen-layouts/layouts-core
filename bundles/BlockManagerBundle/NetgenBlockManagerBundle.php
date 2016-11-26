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
        $container->addCompilerPass(new CompilerPass\LayoutResolver\TargetTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\ConditionTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\DoctrineTargetHandlerPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\ConditionTypePass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\TargetTypePass());
        $container->addCompilerPass(new CompilerPass\View\TemplateResolverPass());
        $container->addCompilerPass(new CompilerPass\View\ViewBuilderPass());
        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\Parameters\FormMapperRegistryPass());
        $container->addCompilerPass(new CompilerPass\Parameters\ParameterFilterRegistryPass());
        $container->addCompilerPass(new CompilerPass\Parameters\ParameterTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\Collection\QueryTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\Item\ValueLoaderRegistryPass());
        $container->addCompilerPass(new CompilerPass\Item\ItemBuilderPass());
        $container->addCompilerPass(new CompilerPass\Item\UrlBuilderPass());
        $container->addCompilerPass(new CompilerPass\Configuration\SourcePass());
        $container->addCompilerPass(new CompilerPass\Configuration\LayoutTypePass());
        $container->addCompilerPass(new CompilerPass\Configuration\BlockTypePass());
    }
}
