<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroupFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class BlockTypeGroupPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.block.registry.block_type_group';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $blockTypes = $container->getParameter('netgen_block_manager.block_types');
        $blockTypeGroups = $container->getParameter('netgen_block_manager.block_type_groups');

        $blockTypeGroups = $this->generateBlockTypeGroupConfig($blockTypeGroups, $blockTypes);
        $container->setParameter('netgen_block_manager.block_type_groups', $blockTypeGroups);

        $blockTypeGroupServices = $this->buildBlockTypeGroups($container, $blockTypeGroups, $blockTypes);

        $registry = $container->findDefinition(self::$serviceName);

        foreach ($blockTypeGroupServices as $identifier => $blockTypeGroupService) {
            $registry->addMethodCall(
                'addBlockTypeGroup',
                [$identifier, new Reference($blockTypeGroupService)]
            );
        }
    }

    /**
     * Generates the block type group configuration from provided block types.
     *
     * @param array $blockTypeGroups
     * @param array $blockTypes
     *
     * @return array
     */
    private function generateBlockTypeGroupConfig(array $blockTypeGroups, array $blockTypes)
    {
        $missingBlockTypes = [];

        // We will add all blocks which are not located in any group to a custom group
        // if it exists
        if (isset($blockTypeGroups['custom'])) {
            foreach (array_keys($blockTypes) as $blockType) {
                foreach ($blockTypeGroups as $blockTypeGroup) {
                    if (in_array($blockType, $blockTypeGroup['block_types'], true)) {
                        continue 2;
                    }
                }

                $missingBlockTypes[] = $blockType;
            }

            $blockTypeGroups['custom']['block_types'] = array_merge(
                $blockTypeGroups['custom']['block_types'],
                $missingBlockTypes
            );
        }

        return $blockTypeGroups;
    }

    /**
     * Builds the block type group objects from provided configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $blockTypeGroups
     * @param array $blockTypes
     *
     * @return array
     */
    private function buildBlockTypeGroups(ContainerBuilder $container, array $blockTypeGroups, array $blockTypes)
    {
        $blockTypeGroupServices = [];

        foreach ($blockTypeGroups as $identifier => $blockTypeGroup) {
            $serviceIdentifier = sprintf('netgen_block_manager.block.block_type_group.%s', $identifier);

            $blockTypeReferences = [];
            foreach ($blockTypeGroup['block_types'] as $blockTypeIdentifier) {
                if (isset($blockTypes[$blockTypeIdentifier])) {
                    $blockTypeReferences[] = new Reference(
                        sprintf(
                            'netgen_block_manager.block.block_type.%s',
                            $blockTypeIdentifier
                        )
                    );
                }
            }

            $container->register($serviceIdentifier, BlockTypeGroup::class)
                ->setArguments([$identifier, $blockTypeGroup, $blockTypeReferences])
                ->setLazy(true)
                ->setPublic(true)
                ->setFactory([BlockTypeGroupFactory::class, 'buildBlockTypeGroup']);

            $blockTypeGroupServices[$identifier] = $serviceIdentifier;
        }

        return $blockTypeGroupServices;
    }
}
