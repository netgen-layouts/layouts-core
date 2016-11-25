<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\BlockManager\Exception\RuntimeException;

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

        $blockTypes = $container->getParameter('netgen_block_manager.block_types');
        $blockTypeGroups = $container->getParameter('netgen_block_manager.block_type_groups');

        $this->validateBlockTypeGroups($container, $blockTypeGroups);
        $this->validateBlockTypes($container, $blockTypes);

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $blockTypeServices = $container->findTaggedServiceIds(self::TAG_NAME);
        $blockTypeGroupServices = $container->findTaggedServiceIds(self::GROUP_TAG_NAME);

        foreach ($blockTypeServices as $blockTypeService => $tag) {
            $registry->addMethodCall(
                'addBlockType',
                array(new Reference($blockTypeService))
            );
        }

        foreach ($blockTypeGroupServices as $blockTypeGroupService => $tag) {
            $registry->addMethodCall(
                'addBlockTypeGroup',
                array(new Reference($blockTypeGroupService))
            );
        }
    }

    /**
     * Validates block type group config.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $blockTypeGroups
     *
     * @throws \RuntimeException If validation failed
     */
    protected function validateBlockTypeGroups(ContainerBuilder $container, array $blockTypeGroups)
    {
        foreach ($blockTypeGroups as $blockTypeGroup => $blockTypeGroupConfig) {
            foreach ($blockTypeGroupConfig['block_types'] as $blockType) {
                if (!$container->has(sprintf('netgen_block_manager.configuration.block_type.%s', $blockType))) {
                    throw new RuntimeException(
                        sprintf(
                            'Block type "%s" used in "%s" block type group does not exist.',
                            $blockType,
                            $blockTypeGroup
                        )
                    );
                }
            }
        }
    }

    /**
     * Validates block type config.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $blockTypes
     *
     * @throws \RuntimeException If validation failed
     */
    protected function validateBlockTypes(ContainerBuilder $container, array $blockTypes)
    {
        foreach ($blockTypes as $blockType => $blockTypeConfig) {
            $definition = $blockTypeConfig['definition_identifier'];
            if (!$container->has(sprintf('netgen_block_manager.block.block_definition.%s', $definition))) {
                throw new RuntimeException(
                    sprintf(
                        'Block definition "%s" used in "%s" block type does not exist.',
                        $blockTypeConfig['definition_identifier'],
                        $blockType
                    )
                );
            }
        }
    }
}
