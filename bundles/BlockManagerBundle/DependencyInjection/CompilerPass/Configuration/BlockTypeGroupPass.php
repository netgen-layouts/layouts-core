<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Configuration\Factory\BlockTypeGroupFactory;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class BlockTypeGroupPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.configuration.registry.block_type';
    const TAG_NAME = 'netgen_block_manager.configuration.block_type_group';

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

        $blockTypeGroups = $this->generateBlockTypeGroupConfig($blockTypeGroups, $blockTypes);
        $container->setParameter('netgen_block_manager.block_type_groups', $blockTypeGroups);

        $this->validateBlockTypeGroups($blockTypeGroups, $blockTypes);
        $this->buildBlockTypeGroups($container, $blockTypeGroups);

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $blockTypeGroupServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($blockTypeGroupServices as $blockTypeGroupService => $tag) {
            $registry->addMethodCall(
                'addBlockTypeGroup',
                array(new Reference($blockTypeGroupService))
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
    protected function generateBlockTypeGroupConfig(array $blockTypeGroups, array $blockTypes)
    {
        $missingBlockTypes = array();

        // We will add all blocks which are not located in any group to a custom group
        // if it exists and is enabled
        if (isset($blockTypeGroups['custom']) && $blockTypeGroups['custom']['enabled']) {
            foreach ($blockTypes as $identifier => $blockType) {
                if (!$blockType['enabled']) {
                    continue;
                }

                foreach ($blockTypeGroups as $blockTypeGroup) {
                    if (in_array($identifier, $blockTypeGroup['block_types'], true)) {
                        continue 2;
                    }
                }

                $missingBlockTypes[] = $identifier;
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
     */
    protected function buildBlockTypeGroups(ContainerBuilder $container, array $blockTypeGroups)
    {
        foreach ($blockTypeGroups as $identifier => $blockTypeGroup) {
            if (!$blockTypeGroup['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.configuration.block_type_group.%s', $identifier);

            $blockTypeReferences = array();
            foreach ($blockTypeGroup['block_types'] as $blockTypeIdentifier) {
                $blockTypeReferences[] = new Reference(
                    sprintf(
                        'netgen_block_manager.configuration.block_type.%s',
                        $blockTypeIdentifier
                    )
                );
            }

            $container->register($serviceIdentifier, BlockTypeGroup::class)
                ->setArguments(array($identifier, $blockTypeGroup, $blockTypeReferences))
                ->addTag('netgen_block_manager.configuration.block_type_group')
                ->setFactory(array(BlockTypeGroupFactory::class, 'buildBlockTypeGroup'));
        }
    }

    /**
     * Validates block type group config.
     *
     * @param array $blockTypeGroups
     * @param array $blockTypes
     *
     * @throws \RuntimeException If validation failed
     */
    protected function validateBlockTypeGroups(array $blockTypeGroups, array $blockTypes)
    {
        foreach ($blockTypeGroups as $identifier => $blockTypeGroup) {
            foreach ($blockTypeGroup['block_types'] as $blockTypeIdentifier) {
                if (!isset($blockTypes[$blockTypeIdentifier])) {
                    throw new RuntimeException(
                        sprintf(
                            'Block type "%s" used in "%s" block type group does not exist.',
                            $blockTypeIdentifier,
                            $identifier
                        )
                    );
                }
            }
        }
    }
}
