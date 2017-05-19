<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config;

use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConfigDefinitionPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.config.registry.config_definition';
    const TAG_NAME = 'netgen_block_manager.config.config_definition_handler';
    const SUPPORTED_TYPES = array('block');

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

        $configDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $configDefinitionHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($configDefinitionHandlers as $configDefinitionHandler => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['type'])) {
                    throw new RuntimeException(
                        "Config definition handler definition must have a 'type' attribute in its' tag."
                    );
                }

                $type = $tag['type'];

                if (!in_array($type, self::SUPPORTED_TYPES, true)) {
                    throw new RuntimeException(
                        sprintf(
                            'Config definition type "%s" is not supported.',
                            $type
                        )
                    );
                }

                if (!isset($tag['identifier'])) {
                    throw new RuntimeException(
                        "Config definition handler definition must have an 'identifier' attribute in its' tag."
                    );
                }

                $identifier = $tag['identifier'];

                $configDefinitionServiceName = sprintf('netgen_block_manager.config.config_definition.%s.%s', $type, $identifier);
                $configDefinitionService = new Definition(ConfigDefinition::class);

                $configDefinitionService->setLazy(true);
                $configDefinitionService->addArgument($type);
                $configDefinitionService->addArgument($identifier);
                $configDefinitionService->addArgument(new Reference($configDefinitionHandler));
                $configDefinitionService->setFactory(array(new Reference('netgen_block_manager.config.config_definition_factory'), 'buildConfigDefinition'));

                $container->setDefinition($configDefinitionServiceName, $configDefinitionService);

                $configDefinitionRegistry->addMethodCall(
                    'addConfigDefinition',
                    array(
                        $type,
                        $identifier,
                        new Reference($configDefinitionServiceName),
                    )
                );
            }
        }
    }
}
