<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class BlockTypeRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.configuration.registry.block_type';
    const TAG_NAME = 'netgen_block_manager.configuration.block_type';
    const GROUP_TAG_NAME = 'netgen_block_manager.configuration.block_type_group';

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
        $blockTypes = $container->findTaggedServiceIds(self::TAG_NAME);
        $blockTypeGroups = $container->findTaggedServiceIds(self::GROUP_TAG_NAME);

        foreach ($blockTypes as $blockType => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException(
                    "Block type service definition must have an 'identifier' attribute in its' tag."
                );
            }

            $registry->addMethodCall(
                'addBlockType',
                array($tag[0]['identifier'], new Reference($blockType))
            );
        }

        foreach ($blockTypeGroups as $blockTypeGroup => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException(
                    "Block type group service definition must have an 'identifier' attribute in its' tag."
                );
            }

            $registry->addMethodCall(
                'addBlockTypeGroup',
                array($tag[0]['identifier'], new Reference($blockTypeGroup))
            );
        }
    }
}
