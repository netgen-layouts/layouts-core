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
    const TAG_NAMES = array(
        'block' => 'netgen_block_manager.block.config_definition_handler',
    );

    private $seenConfigKeys = array();

    public function process(ContainerBuilder $container)
    {
        foreach (self::TAG_NAMES as $type => $tagName) {
            $configDefinitionHandlers = $container->findTaggedServiceIds($tagName);

            foreach ($configDefinitionHandlers as $configDefinitionHandler => $tags) {
                foreach ($tags as $tag) {
                    if (!isset($tag['config_key'])) {
                        throw new RuntimeException(
                            "Config definition handler definition must have an 'config_key' attribute in its' tag."
                        );
                    }

                    $configKey = $tag['config_key'];

                    if (isset($this->seenConfigKeys[$type][$configKey])) {
                        throw new RuntimeException(
                            sprintf(
                                "Config definition with '%s' config key is defined more than once for '%s' config type.",
                                $configKey,
                                $type
                            )
                        );
                    }

                    $this->seenConfigKeys[$type][$configKey] = true;

                    $configDefinitionServiceName = sprintf('netgen_block_manager.%s.config_definition.%s', $type, $configKey);
                    $configDefinitionService = new Definition(ConfigDefinition::class);

                    $configDefinitionService->setLazy(true);
                    $configDefinitionService->setPublic(false);
                    $configDefinitionService->addArgument($configKey);
                    $configDefinitionService->addArgument(new Reference($configDefinitionHandler));
                    $configDefinitionService->setFactory(array(new Reference('netgen_block_manager.config.config_definition_factory'), 'buildConfigDefinition'));

                    $container->setDefinition($configDefinitionServiceName, $configDefinitionService);
                }
            }
        }
    }
}
