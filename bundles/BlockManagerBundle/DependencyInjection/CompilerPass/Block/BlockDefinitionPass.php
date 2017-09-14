<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\TwigBlockDefinition;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BlockDefinitionPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.block.registry.block_definition';
    const TAG_NAME = 'netgen_block_manager.block.block_definition_handler';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $blockDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $blockDefinitionHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        $blockDefinitions = $container->getParameter('netgen_block_manager.block_definitions');
        foreach ($blockDefinitions as $identifier => $blockDefinition) {
            $handlerIdentifier = $identifier;
            if (!empty($blockDefinition['handler'])) {
                $handlerIdentifier = $blockDefinition['handler'];
            }

            $configServiceName = sprintf('netgen_block_manager.block.block_definition.configuration.%s', $identifier);
            $configService = new Definition(Configuration::class);

            $configService->setArguments(array($identifier, $blockDefinition));
            $configService->setPublic(false);
            $configService->setFactory(array(Factory::class, 'buildConfig'));

            $container->setDefinition($configServiceName, $configService);

            $foundHandler = null;
            foreach ($blockDefinitionHandlers as $blockDefinitionHandler => $tags) {
                foreach ($tags as $tag) {
                    if (!isset($tag['identifier'])) {
                        throw new RuntimeException(
                            "Block definition handler definition must have an 'identifier' attribute in its' tag."
                        );
                    }

                    if ($tag['identifier'] === $handlerIdentifier) {
                        $foundHandler = $blockDefinitionHandler;
                        break 2;
                    }
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

            $handlerClass = $container->findDefinition($foundHandler)->getClass();
            if (strpos($handlerClass, '%') === 0) {
                $handlerClass = $container->getParameter(
                    str_replace('%', '', $handlerClass)
                );
            }

            $factoryMethod = 'buildBlockDefinition';
            $definitionClass = BlockDefinition::class;

            if (is_a($handlerClass, ContainerDefinitionHandlerInterface::class, true)) {
                $factoryMethod = 'buildContainerDefinition';
                $definitionClass = ContainerDefinition::class;
            } elseif (is_a($handlerClass, TwigBlockDefinitionHandlerInterface::class, true)) {
                $factoryMethod = 'buildTwigBlockDefinition';
                $definitionClass = TwigBlockDefinition::class;
            }

            $blockDefinitionServiceName = sprintf('netgen_block_manager.block.block_definition.%s', $identifier);
            $blockDefinitionService = new Definition($definitionClass);

            $blockDefinitionService->setLazy(true);
            $blockDefinitionService->addArgument($identifier);
            $blockDefinitionService->addArgument(new Reference($foundHandler));
            $blockDefinitionService->addArgument(new Reference($configServiceName));
            $blockDefinitionService->addArgument(
                array(
                    new Reference('netgen_block_manager.block.config_definition.http_cache'),
                )
            );

            $blockDefinitionService->setFactory(array(new Reference('netgen_block_manager.block.block_definition_factory'), $factoryMethod));

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
