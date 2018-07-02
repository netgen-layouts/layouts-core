<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeFactory;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class BlockTypePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.block.registry.block_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $blockTypes = $container->getParameter('netgen_block_manager.block_types');
        $blockDefinitions = $container->getParameter('netgen_block_manager.block_definitions');

        $blockTypes = $this->generateBlockTypeConfig($blockTypes, $blockDefinitions);
        $container->setParameter('netgen_block_manager.block_types', $blockTypes);

        $this->validateBlockTypes($blockTypes, $blockDefinitions);
        $blockTypeServices = $this->buildBlockTypes($container, $blockTypes);

        $registry = $container->findDefinition(self::SERVICE_NAME);

        $registry->replaceArgument(0, $blockTypeServices);
    }

    /**
     * Generates the block type configuration from provided block definitions.
     */
    private function generateBlockTypeConfig(array $blockTypes, array $blockDefinitions): array
    {
        foreach ($blockDefinitions as $identifier => $blockDefinition) {
            if (
                !empty($blockTypes[$identifier]['definition_identifier']) &&
                $blockTypes[$identifier]['definition_identifier'] !== $identifier
            ) {
                // We skip the block types which have been completely redefined
                // i.e. had the block definition identifier changed
                continue;
            }

            if (!isset($blockTypes[$identifier])) {
                $blockTypes[$identifier] = [
                    'name' => $blockDefinition['name'],
                    'icon' => $blockDefinition['icon'],
                    'enabled' => $blockDefinition['enabled'],
                    'definition_identifier' => $identifier,
                    'defaults' => [],
                ];

                continue;
            }

            if (!$blockDefinition['enabled']) {
                $blockTypes[$identifier]['enabled'] = false;
            } elseif (!isset($blockTypes[$identifier]['enabled'])) {
                $blockTypes[$identifier]['enabled'] = true;
            }

            $blockTypes[$identifier] += [
                'name' => $blockDefinition['name'],
                'icon' => $blockDefinition['icon'],
                'definition_identifier' => $identifier,
            ];
        }

        foreach ($blockTypes as $identifier => $blockType) {
            $definitionIdentifier = $blockType['definition_identifier'] ?? $identifier;

            if (!isset($blockDefinitions[$definitionIdentifier])) {
                continue;
            }

            if (!$blockDefinitions[$definitionIdentifier]['enabled']) {
                $blockTypes[$identifier]['enabled'] = false;
            }
        }

        return $blockTypes;
    }

    /**
     * Builds the block type objects from provided configuration.
     */
    private function buildBlockTypes(ContainerBuilder $container, array $blockTypes): array
    {
        $blockTypeServices = [];

        foreach ($blockTypes as $identifier => $blockType) {
            $serviceIdentifier = sprintf('netgen_block_manager.block.block_type.%s', $identifier);

            $container->register($serviceIdentifier, BlockType::class)
                ->setArguments(
                    [
                        $identifier,
                        $blockType,
                        new Reference(
                            sprintf(
                                'netgen_block_manager.block.block_definition.%s',
                                $blockType['definition_identifier']
                            )
                        ),
                    ]
                )
                ->setLazy(true)
                ->setPublic(true)
                ->setFactory([BlockTypeFactory::class, 'buildBlockType']);

            $blockTypeServices[$identifier] = new Reference($serviceIdentifier);
        }

        return $blockTypeServices;
    }

    /**
     * Validates block type config.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If validation failed
     */
    private function validateBlockTypes(array $blockTypes, array $blockDefinitions): void
    {
        foreach ($blockTypes as $identifier => $blockType) {
            if (!isset($blockDefinitions[$blockType['definition_identifier']])) {
                throw new RuntimeException(
                    sprintf(
                        'Block definition "%s" used in "%s" block type does not exist.',
                        $blockType['definition_identifier'],
                        $identifier
                    )
                );
            }
        }
    }
}
