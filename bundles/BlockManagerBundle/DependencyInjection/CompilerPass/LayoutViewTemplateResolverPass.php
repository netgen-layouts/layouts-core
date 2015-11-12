<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LayoutViewTemplateResolverPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.view.template_resolver.layout';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $templateResolver = $container->findDefinition(self::SERVICE_NAME);

        $matcherServices = $container->findTaggedServiceIds('netgen_block_manager.view.layout_matcher');
        $matchers = array();

        foreach ($matcherServices as $serviceName => $tag) {
            $matchers[$tag[0]['identifier']] = new Reference($serviceName);
        }

        $templateResolver->replaceArgument(0, $matchers);
    }
}
