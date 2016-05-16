<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class LayoutTypeRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.configuration.registry.layout_type';
    const TAG_NAME = 'netgen_block_manager.configuration.layout_type';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            if (!$container->has(self::SERVICE_NAME)) {
                throw new RuntimeException("Service '{self::SERVICE_NAME}' is missing.");
            }
        }

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $layoutTypes = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($layoutTypes as $layoutType => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException(
                    "Layout type service definition must have an 'identifier' attribute in its' tag."
                );
            }

            $registry->addMethodCall(
                'addLayoutType',
                array($tag[0]['identifier'], new Reference($layoutType))
            );
        }
    }
}
