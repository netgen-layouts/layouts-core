<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block;

use Generator;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Netgen\Layouts\Block\BlockType\BlockTypeGroupFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function array_keys;
use function in_array;
use function iterator_to_array;
use function sprintf;

final class BlockTypeGroupPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.block.registry.block_type_group';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        /** @var array<string, mixed[]> $blockTypes */
        $blockTypes = $container->getParameter('netgen_layouts.block_types');

        /** @var array<string, mixed[]> $blockTypeGroups */
        $blockTypeGroups = $container->getParameter('netgen_layouts.block_type_groups');

        $blockTypeGroups = $this->generateBlockTypeGroupConfig($blockTypeGroups, $blockTypes);
        $container->setParameter('netgen_layouts.block_type_groups', $blockTypeGroups);

        $blockTypeGroupServices = iterator_to_array($this->buildBlockTypeGroups($container, $blockTypeGroups, $blockTypes));

        $registry = $container->findDefinition(self::SERVICE_NAME);

        $registry->replaceArgument(0, $blockTypeGroupServices);
    }

    /**
     * Generates the block type group configuration from provided block types.
     *
     * @param array<string, array> $blockTypeGroups
     * @param array<string, array> $blockTypes
     *
     * @return array<string, array>
     */
    private function generateBlockTypeGroupConfig(array $blockTypeGroups, array $blockTypes): array
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

            $blockTypeGroups['custom']['block_types'] = [
                ...$blockTypeGroups['custom']['block_types'],
                ...$missingBlockTypes,
            ];
        }

        return $blockTypeGroups;
    }

    /**
     * Builds the block type group objects from provided configuration.
     *
     * @param array<string, array> $blockTypeGroups
     * @param array<string, array> $blockTypes
     *
     * @return \Generator<string, \Symfony\Component\DependencyInjection\Reference>
     */
    private function buildBlockTypeGroups(ContainerBuilder $container, array $blockTypeGroups, array $blockTypes): Generator
    {
        foreach ($blockTypeGroups as $identifier => $blockTypeGroup) {
            $serviceIdentifier = sprintf('netgen_layouts.block.block_type_group.%s', $identifier);

            $blockTypeReferences = [];
            foreach ($blockTypeGroup['block_types'] as $blockTypeIdentifier) {
                if (isset($blockTypes[$blockTypeIdentifier])) {
                    $blockTypeReferences[] = new Reference(
                        sprintf(
                            'netgen_layouts.block.block_type.%s',
                            $blockTypeIdentifier,
                        ),
                    );
                }
            }

            $container->register($serviceIdentifier, BlockTypeGroup::class)
                ->setArguments([$identifier, $blockTypeGroup, $blockTypeReferences])
                ->setLazy(true)
                ->setPublic(false)
                ->setFactory([BlockTypeGroupFactory::class, 'buildBlockTypeGroup']);

            yield $identifier => new Reference($serviceIdentifier);
        }
    }
}
