<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block;

use Generator;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\BlockType\BlockTypeFactory;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function iterator_to_array;
use function sprintf;

final class BlockTypePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.block.registry.block_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        /** @var array<string, mixed[]> $blockTypes */
        $blockTypes = $container->getParameter('netgen_layouts.block_types');

        /** @var array<string, mixed[]> $blockDefinitions */
        $blockDefinitions = $container->getParameter('netgen_layouts.block_definitions');

        $blockTypes = $this->generateBlockTypeConfig($blockTypes, $blockDefinitions);
        $container->setParameter('netgen_layouts.block_types', $blockTypes);

        $this->validateBlockTypes($blockTypes, $blockDefinitions);
        $blockTypeServices = iterator_to_array($this->buildBlockTypes($container, $blockTypes));

        $registry = $container->findDefinition(self::SERVICE_NAME);

        $registry->replaceArgument(0, $blockTypeServices);
    }

    /**
     * Generates the block type configuration from provided block definitions.
     *
     * @param array<string, mixed[]> $blockTypes
     * @param array<string, mixed[]> $blockDefinitions
     *
     * @return array<string, mixed[]>
     */
    private function generateBlockTypeConfig(array $blockTypes, array $blockDefinitions): array
    {
        foreach ($blockDefinitions as $identifier => $blockDefinition) {
            if (
                isset($blockTypes[$identifier]['definition_identifier'])
                && $blockTypes[$identifier]['definition_identifier'] !== ''
                && $blockTypes[$identifier]['definition_identifier'] !== $identifier
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
                    'defaults' => $blockDefinition['defaults'],
                    'definition_identifier' => $identifier,
                ];

                continue;
            }

            $blockTypes[$identifier]['enabled'] ??= true;
            if ($blockDefinition['enabled'] === false) {
                $blockTypes[$identifier]['enabled'] = false;
            }

            $blockTypes[$identifier] += [
                'name' => $blockDefinition['name'],
                'icon' => $blockDefinition['icon'],
                'defaults' => $blockDefinition['defaults'],
                'definition_identifier' => $identifier,
            ];
        }

        foreach ($blockTypes as $identifier => $blockType) {
            $definitionIdentifier = $blockType['definition_identifier'] ?? $identifier;

            if (!isset($blockDefinitions[$definitionIdentifier])) {
                continue;
            }

            if (!isset($blockTypes[$identifier]['icon'])) {
                $blockTypes[$identifier]['icon'] = $blockDefinitions[$definitionIdentifier]['icon'];
            }

            if (!isset($blockTypes[$identifier]['defaults'])) {
                $blockTypes[$identifier]['defaults'] = $blockDefinitions[$definitionIdentifier]['defaults'];
            }

            if ($blockDefinitions[$definitionIdentifier]['enabled'] === false) {
                $blockTypes[$identifier]['enabled'] = false;
            }
        }

        return $blockTypes;
    }

    /**
     * Builds the block type objects from provided configuration.
     *
     * @param array<string, mixed[]> $blockTypes
     *
     * @return \Generator<string, \Symfony\Component\DependencyInjection\Reference>
     */
    private function buildBlockTypes(ContainerBuilder $container, array $blockTypes): Generator
    {
        foreach ($blockTypes as $identifier => $blockType) {
            $serviceIdentifier = sprintf('netgen_layouts.block.block_type.%s', $identifier);

            $container->register($serviceIdentifier, BlockType::class)
                ->setArguments(
                    [
                        $identifier,
                        $blockType,
                        new Reference(
                            sprintf(
                                'netgen_layouts.block.block_definition.%s',
                                $blockType['definition_identifier'],
                            ),
                        ),
                    ],
                )
                ->setLazy(true)
                ->setPublic(false)
                ->setFactory([BlockTypeFactory::class, 'buildBlockType']);

            yield $identifier => new Reference($serviceIdentifier);
        }
    }

    /**
     * Validates block type config.
     *
     * @param array<string, mixed[]> $blockTypes
     * @param array<string, mixed[]> $blockDefinitions
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If validation failed
     */
    private function validateBlockTypes(array $blockTypes, array $blockDefinitions): void
    {
        foreach ($blockTypes as $identifier => $blockType) {
            if (!isset($blockDefinitions[$blockType['definition_identifier']])) {
                throw new RuntimeException(
                    sprintf(
                        'Block definition "%s" used in "%s" block type does not exist.',
                        $blockType['definition_identifier'],
                        $identifier,
                    ),
                );
            }
        }
    }
}
