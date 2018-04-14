<?php

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

    public function process(ContainerBuilder $container)
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
                array($identifier, new Reference($layoutTypeService))
            );
        }
    }

    /**
     * Builds the layout type objects from provided configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $layoutTypes
     *
     * @return array
     */
    private function buildLayoutTypes(ContainerBuilder $container, array $layoutTypes)
    {
        $layoutTypeServices = array();

        foreach ($layoutTypes as $identifier => $layoutType) {
            $serviceIdentifier = sprintf('netgen_block_manager.layout.layout_type.%s', $identifier);

            $container->register($serviceIdentifier, LayoutType::class)
                ->setArguments(array($identifier, $layoutType))
                ->setLazy(true)
                ->setPublic(true)
                ->setFactory(array(LayoutTypeFactory::class, 'buildLayoutType'));

            $layoutTypeServices[$identifier] = $serviceIdentifier;
        }

        return $layoutTypeServices;
    }

    /**
     * Validates layout type config.
     *
     * @param array $layoutTypes
     * @param array $blockDefinitions
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If validation failed
     */
    private function validateLayoutTypes(array $layoutTypes, array $blockDefinitions)
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
