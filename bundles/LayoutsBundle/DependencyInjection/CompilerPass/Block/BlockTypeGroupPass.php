<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block;

use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function array_column;
use function array_key_exists;
use function array_keys;
use function in_array;
use function sprintf;
use function uasort;

final class BlockTypeGroupPass implements CompilerPassInterface
{
    private const string SERVICE_NAME = 'netgen_layouts.block.registry.block_type_group';

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

        $blockTypeGroupServices = [...$this->buildBlockTypeGroups($container, $blockTypeGroups, $blockTypes)];

        $registry = $container->findDefinition(self::SERVICE_NAME);

        $registry->replaceArgument(0, $blockTypeGroupServices);
    }

    /**
     * Generates the block type group configuration from provided block types.
     *
     * @param array<string, mixed[]> $blockTypeGroups
     * @param array<string, mixed[]> $blockTypes
     *
     * @return array<string, mixed[]>
     */
    private function generateBlockTypeGroupConfig(array $blockTypeGroups, array $blockTypes): array
    {
        $missingBlockTypes = [];

        // We will add all blocks which are not located in any group to a custom group
        // if it exists
        if (isset($blockTypeGroups['custom'])) {
            foreach (array_keys($blockTypes) as $blockType) {
                foreach ($blockTypeGroups as $blockTypeGroup) {
                    if (in_array($blockType, array_column($blockTypeGroup['block_types'], 'identifier'), true)) {
                        continue 2;
                    }
                }

                $missingBlockTypes[] = ['identifier' => $blockType, 'priority' => 0];
            }

            $blockTypeGroups['custom']['block_types'] = [
                ...$blockTypeGroups['custom']['block_types'],
                ...$missingBlockTypes,
            ];
        }

        uasort(
            $blockTypeGroups,
            static fn (array $group1, array $group2): int => $group2['priority'] <=> $group1['priority'],
        );

        return $blockTypeGroups;
    }

    /**
     * Builds the block type group objects from provided configuration.
     *
     * @param array<string, mixed[]> $blockTypeGroups
     * @param array<string, mixed[]> $blockTypes
     *
     * @return iterable<string, \Symfony\Component\DependencyInjection\Reference>
     */
    private function buildBlockTypeGroups(ContainerBuilder $container, array $blockTypeGroups, array $blockTypes): iterable
    {
        foreach ($blockTypeGroups as $identifier => $blockTypeGroup) {
            $serviceIdentifier = sprintf('netgen_layouts.block.block_type_group.%s', $identifier);

            $blockTypeReferences = [];
            $groupBlockTypes = $blockTypeGroup['block_types'];

            uasort(
                $groupBlockTypes,
                static fn (array $type1, array $type2): int => $type2['priority'] <=> $type1['priority'],
            );

            foreach ($groupBlockTypes as $groupBlockType) {
                if (array_key_exists($groupBlockType['identifier'], $blockTypes)) {
                    $blockTypeReferences[] = new Reference(
                        sprintf(
                            'netgen_layouts.block.block_type.%s',
                            $groupBlockType['identifier'],
                        ),
                    );
                }
            }

            $container->register($serviceIdentifier, BlockTypeGroup::class)
                ->setArguments([$identifier, $blockTypeGroup, $blockTypeReferences])
                ->setFactory([new Reference('netgen_layouts.block.block_type_group_factory'), 'buildBlockTypeGroup']);

            yield $identifier => new Reference($serviceIdentifier);
        }
    }
}
