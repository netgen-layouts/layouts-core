<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class SourceRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.configuration.registry.source';
    const TAG_NAME = 'netgen_block_manager.configuration.source';

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

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $sources = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($sources as $source => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException(
                    "Source service definition must have an 'identifier' attribute in its' tag."
                );
            }

            $registry->addMethodCall(
                'addSource',
                array($tag[0]['identifier'], new Reference($source))
            );
        }
    }
}
