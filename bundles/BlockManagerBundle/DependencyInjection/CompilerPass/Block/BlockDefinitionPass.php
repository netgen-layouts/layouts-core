<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BlockDefinitionPass implements CompilerPassInterface
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
            if (!$blockDefinition['enabled']) {
                continue;
            }

            $configServiceName = sprintf('netgen_block_manager.block.block_definition.configuration.%s', $identifier);
            $configService = new Definition(Configuration::class);

            $configService->setArguments(array($identifier, $blockDefinition));
            $configService->setPublic(false);
            $configService->setFactory(array(Factory::class, 'buildConfig'));

            $container->setDefinition($configServiceName, $configService);

            $foundHandler = null;
            foreach ($blockDefinitionHandlers as $blockDefinitionHandler => $tag) {
                if (!isset($tag[0]['identifier'])) {
                    throw new RuntimeException(
                        "Block definition handler definition must have an 'identifier' attribute in its' tag."
                    );
                }

                if ($tag[0]['identifier'] === $identifier) {
                    $foundHandler = $blockDefinitionHandler;
                    break;
                }
            }

            if ($foundHandler === null) {
                throw new RuntimeException(
                    sprintf(
                        'Block definition handler for "%s" block definition does not exist.',
                        $identifier
                    )
                );
            }

            $blockDefinitionServiceName = sprintf('netgen_block_manager.block.block_definition.%s', $identifier);
            $blockDefinitionService = new Definition(BlockDefinition::class);

            $blockDefinitionService->setLazy(true);
            $blockDefinitionService->addArgument($identifier);
            $blockDefinitionService->addArgument(new Reference($foundHandler));
            $blockDefinitionService->addArgument(new Reference($configServiceName));
            $blockDefinitionService->addArgument(new Reference('netgen_block_manager.parameters.parameter_builder'));
            $blockDefinitionService->setFactory(array(BlockDefinitionFactory::class, 'buildBlockDefinition'));

            $container->setDefinition($blockDefinitionServiceName, $blockDefinitionService);

            $blockDefinitionRegistry->addMethodCall(
                'addBlockDefinition',
                array(
                    $identifier,
                    new Reference($blockDefinitionServiceName),
                )
            );
        }
    }
}
