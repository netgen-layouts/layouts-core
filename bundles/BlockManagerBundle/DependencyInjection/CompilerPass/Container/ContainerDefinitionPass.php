<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Container\ContainerDefinition;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Factory;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Layout\Container\ContainerDefinitionFactory;
use Netgen\BlockManager\Layout\Container\DynamicContainerDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ContainerDefinitionPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.container.registry.container_definition';
    const TAG_NAME = 'netgen_block_manager.container.container_definition_handler';

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

        $containerDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $containerDefinitionHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        $containerDefinitions = $container->getParameter('netgen_block_manager.container_definitions');
        foreach ($containerDefinitions as $identifier => $containerDefinition) {
            if (!$containerDefinition['enabled']) {
                continue;
            }

            $handlerIdentifier = $identifier;
            if (!empty($containerDefinition['handler'])) {
                $handlerIdentifier = $containerDefinition['handler'];
            }

            $configServiceName = sprintf('netgen_block_manager.container.container_definition.configuration.%s', $identifier);
            $configService = new Definition(Configuration::class);

            $configService->setArguments(array($identifier, $containerDefinition));
            $configService->setPublic(false);
            $configService->setFactory(array(Factory::class, 'buildConfig'));

            $container->setDefinition($configServiceName, $configService);

            $foundHandler = null;
            foreach ($containerDefinitionHandlers as $containerDefinitionHandler => $tag) {
                if (!isset($tag[0]['identifier'])) {
                    throw new RuntimeException(
                        "Block definition handler definition must have an 'identifier' attribute in its' tag."
                    );
                }

                if ($tag[0]['identifier'] === $handlerIdentifier) {
                    $foundHandler = $containerDefinitionHandler;
                    break;
                }
            }

            if ($foundHandler === null) {
                throw new RuntimeException(
                    sprintf(
                        'Block definition handler "%s" for "%s" block definition does not exist.',
                        $handlerIdentifier,
                        $identifier
                    )
                );
            }

            $definitionClass = ContainerDefinition::class;
            $handlerDefinition = $container->getDefinition($foundHandler);
            if (is_a($handlerDefinition->getClass(), DynamicContainerDefinitionHandlerInterface::class, true)) {
                $definitionClass = DynamicContainerDefinition::class;
            }

            $containerDefinitionServiceName = sprintf('netgen_block_manager.container.container_definition.%s', $identifier);
            $containerDefinitionService = new Definition($definitionClass);

            $containerDefinitionService->setLazy(true);
            $containerDefinitionService->addArgument($identifier);
            $containerDefinitionService->addArgument(new Reference($foundHandler));
            $containerDefinitionService->addArgument(new Reference($configServiceName));
            $containerDefinitionService->addArgument(new Reference('netgen_block_manager.parameters.parameter_builder'));
            $containerDefinitionService->setFactory(array(ContainerDefinitionFactory::class, 'buildContainerDefinition'));

            $container->setDefinition($containerDefinitionServiceName, $containerDefinitionService);

            $containerDefinitionRegistry->addMethodCall(
                'addContainerDefinition',
                array(
                    $identifier,
                    new Reference($containerDefinitionServiceName),
                )
            );
        }
    }
}
