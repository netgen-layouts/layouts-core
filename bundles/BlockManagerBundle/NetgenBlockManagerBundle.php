<?php

namespace Netgen\Bundle\BlockManagerBundle;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockDefinition\BlockDefinitionRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypeRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ResultValueBuilderPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ValueLoaderRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetBuilderRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\ConditionMatcherRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineRuleHandlerPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\ViewBuilderPass;
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

        $container->addCompilerPass(new BlockDefinitionRegistryPass());
        $container->addCompilerPass(new TargetBuilderRegistryPass());
        $container->addCompilerPass(new ConditionMatcherRegistryPass());
        $container->addCompilerPass(new DoctrineRuleHandlerPass());
        $container->addCompilerPass(new TemplateResolverPass());
        $container->addCompilerPass(new ViewBuilderPass());
        $container->addCompilerPass(new FormMapperPass());
        $container->addCompilerPass(new QueryTypeRegistryPass());
        $container->addCompilerPass(new ValueLoaderRegistryPass());
        $container->addCompilerPass(new ResultValueBuilderPass());
    }
}
