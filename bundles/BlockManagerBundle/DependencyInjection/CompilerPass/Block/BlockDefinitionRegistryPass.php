<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class BlockDefinitionRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.block.registry.block_definition';
    const TAG_NAME = 'netgen_block_manager.block.block_definition_handler';

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

        $blockDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $blockDefinitionHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        $blockDefinitions = $container->getParameter('netgen_block_manager.block_definitions');
        foreach ($blockDefinitions as $identifier => $blockDefinition) {
            $configServiceName = sprintf('netgen_block_manager.block.block_definition.configuration.%s', $identifier);
            $configService = new Definition(
                $container->getParameter('netgen_block_manager.block.block_definition.configuration.class')
            );

            $configService->setArguments(array($identifier, $blockDefinition));
            $configService->setFactory(
                array(
                    $container->getParameter('netgen_block_manager.block.block_definition.configuration.factory.class'),
                    'buildBlockDefinitionConfig',
                )
            );

            $container->setDefinition($configServiceName, $configService);

            foreach ($blockDefinitionHandlers as $blockDefinitionHandler => $tag) {
                if (!isset($tag[0]['identifier'])) {
                    throw new RuntimeException(
                        "Block definition handler definition must have an 'identifier' attribute in its' tag."
                    );
                }

                if ($tag[0]['identifier'] !== $identifier) {
                    continue;
                }

                $blockDefinitionServiceName = sprintf('netgen_block_manager.block.block_definition.%s', $identifier);
                $blockDefinitionService = new Definition(
                    $container->getParameter('netgen_block_manager.block.block_definition.class')
                );

                $blockDefinitionService->addArgument($identifier);
                $blockDefinitionService->addArgument(new Reference($blockDefinitionHandler));
                $blockDefinitionService->addArgument(new Reference($configServiceName));
                $container->setDefinition($blockDefinitionServiceName, $blockDefinitionService);

                $blockDefinitionRegistry->addMethodCall(
                    'addBlockDefinition',
                    array(new Reference($blockDefinitionServiceName))
                );
            }
        }
    }
}
