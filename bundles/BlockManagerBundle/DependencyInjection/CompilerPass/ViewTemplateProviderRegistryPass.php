<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ViewTemplateProviderRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.registry.view_template_provider';
    const TAG_NAME = 'netgen_block_manager.view.template_provider';

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

        $viewTemplateProviderRegistry = $container->findDefinition(self::SERVICE_NAME);
        $viewTemplateProviders = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($viewTemplateProviders as $serviceName => $tag) {
            $viewTemplateProviderRegistry->addMethodCall(
                'addViewTemplateProvider',
                array(new Reference($serviceName), $tag[0]['type'])
            );
        }
    }
}
