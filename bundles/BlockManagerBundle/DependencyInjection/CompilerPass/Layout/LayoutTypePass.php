<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\LayoutTypeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class LayoutTypePass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.layout.registry.layout_type';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $layoutTypes = $container->getParameter('netgen_block_manager.layout_types');
        $blockDefinitions = $container->getParameter('netgen_block_manager.block_definitions');

        $this->validateLayoutTypes($layoutTypes, $blockDefinitions);
        $layoutTypeServices = $this->buildLayoutTypes($container, $layoutTypes);

        $registry = $container->findDefinition(self::$serviceName);

        foreach ($layoutTypeServices as $identifier => $layoutTypeService) {
            $registry->addMethodCall(
                'addLayoutType',
                [$identifier, new Reference($layoutTypeService)]
            );
        }
    }

    /**
     * Builds the layout type objects from provided configuration.
     */
    private function buildLayoutTypes(ContainerBuilder $container, array $layoutTypes): array
    {
        $layoutTypeServices = [];

        foreach ($layoutTypes as $identifier => $layoutType) {
            $serviceIdentifier = sprintf('netgen_block_manager.layout.layout_type.%s', $identifier);

            $container->register($serviceIdentifier, LayoutType::class)
                ->setArguments([$identifier, $layoutType])
                ->setLazy(true)
                ->setPublic(true)
                ->setFactory([LayoutTypeFactory::class, 'buildLayoutType']);

            $layoutTypeServices[$identifier] = $serviceIdentifier;
        }

        return $layoutTypeServices;
    }

    /**
     * Validates layout type config.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If validation failed
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
                                $layoutType
                            )
                        );
                    }
                }
            }
        }
    }
}
