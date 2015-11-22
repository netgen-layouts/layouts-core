<?php

namespace Netgen\Bundle\BlockManagerBundle;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockDefinitionRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\TargetBuilderRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\ConditionMatcherRegistryPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineRuleHandlerPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutViewTemplateResolverPass;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockViewTemplateResolverPass;
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

        $container->addCompilerPass(new BlockDefinitionRegistryPass());
        $container->addCompilerPass(new TargetBuilderRegistryPass());
        $container->addCompilerPass(new ConditionMatcherRegistryPass());
        $container->addCompilerPass(new DoctrineRuleHandlerPass());
        $container->addCompilerPass(new LayoutViewTemplateResolverPass());
        $container->addCompilerPass(new BlockViewTemplateResolverPass());
        $container->addCompilerPass(new ViewBuilderPass());
    }
}
