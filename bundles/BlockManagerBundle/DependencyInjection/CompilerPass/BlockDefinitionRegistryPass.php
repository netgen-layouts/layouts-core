<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BlockDefinitionRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.block_definition.registry';
    const TAG_NAME = 'netgen_block_manager.block_definition';

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
        $blockDefinitions = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($blockDefinitions as $blockDefinition) {
            $blockDefinitionRegistry->addMethodCall(
                'addBlockDefinition',
                array(new Reference($blockDefinition))
            );
        }
    }
}
