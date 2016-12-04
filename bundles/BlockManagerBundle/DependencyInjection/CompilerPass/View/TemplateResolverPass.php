<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TemplateResolverPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.view.template_resolver';
    const TAG_NAME = 'netgen_block_manager.view.template_matcher';

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
        $matcherServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $matchers = array();
        foreach ($matcherServices as $serviceName => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException(
                    "Matcher service definition must have an 'identifier' attribute in its' tag."
                );
            }

            $matchers[$tag[0]['identifier']] = new Reference($serviceName);
        }

        $templateResolver->replaceArgument(0, $matchers);
    }
}
