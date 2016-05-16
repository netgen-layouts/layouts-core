<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class BlockDefinitionRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.block.registry.block_definition';
    const TAG_NAME = 'netgen_block_manager.block.block_definition';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            throw new RuntimeException("Service '{self::SERVICE_NAME}' is missing.");
        }

        $blockDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $blockDefinitions = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($blockDefinitions as $blockDefinition => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException(
                    "Block definition service definition must have an 'identifier' attribute in its' tag."
                );
            }

            $configService = sprintf('netgen_block_manager.configuration.block_definition.%s', $tag[0]['identifier']);
            if (!$container->has($configService)) {
                throw new RuntimeException(
                    sprintf('Block definition "%s" does not have a configuration.', $tag[0]['identifier'])
                );
            }

            $blockDefinitionService = $container->findDefinition($blockDefinition);
            $blockDefinitionService->addMethodCall('setConfiguration', array(new Reference($configService)));

            $blockDefinitionRegistry->addMethodCall(
                'addBlockDefinition',
                array(new Reference($blockDefinition))
            );
        }
    }
}
