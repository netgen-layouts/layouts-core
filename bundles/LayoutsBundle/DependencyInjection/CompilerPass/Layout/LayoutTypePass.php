<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout;

use Generator;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\LayoutTypeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function iterator_to_array;
use function sprintf;

final class LayoutTypePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.layout.registry.layout_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        /** @var array<string, mixed[]> $layoutTypes */
        $layoutTypes = $container->getParameter('netgen_layouts.layout_types');

        /** @var array<string, mixed[]> $blockDefinitions */
        $blockDefinitions = $container->getParameter('netgen_layouts.block_definitions');

        $this->validateLayoutTypes($layoutTypes, $blockDefinitions);
        $layoutTypeServices = iterator_to_array($this->buildLayoutTypes($container, $layoutTypes));

        $registry = $container->findDefinition(self::SERVICE_NAME);

        $registry->replaceArgument(0, $layoutTypeServices);
    }

    /**
     * Builds the layout type objects from provided configuration.
     *
     * @param array<string, mixed[]> $layoutTypes
     *
     * @return \Generator<string, \Symfony\Component\DependencyInjection\Reference>
     */
    private function buildLayoutTypes(ContainerBuilder $container, array $layoutTypes): Generator
    {
        foreach ($layoutTypes as $identifier => $layoutType) {
            $serviceIdentifier = sprintf('netgen_layouts.layout.layout_type.%s', $identifier);

            $container->register($serviceIdentifier, LayoutType::class)
                ->setArguments([$identifier, $layoutType])
                ->setLazy(true)
                ->setPublic(false)
                ->setFactory([LayoutTypeFactory::class, 'buildLayoutType']);

            yield $identifier => new Reference($serviceIdentifier);
        }
    }

    /**
     * Validates layout type config.
     *
     * @param array<string, mixed[]> $layoutTypes
     * @param array<string, mixed[]> $blockDefinitions
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If validation failed
     */
    private function validateLayoutTypes(array $layoutTypes, array $blockDefinitions): void
    {
        foreach ($layoutTypes as $layoutType => $layoutTypeConfig) {
            foreach ($layoutTypeConfig['zones'] as $zoneConfig) {
                foreach ($zoneConfig['allowed_block_definitions'] as $blockDefinition) {
                    if (!isset($blockDefinitions[$blockDefinition])) {
                        throw new RuntimeException(
                            sprintf(
                                'Block definition "%s" used in "%s" layout type does not exist.',
                                $blockDefinition,
                                $layoutType,
                            ),
                        );
                    }
                }
            }
        }
    }
}
