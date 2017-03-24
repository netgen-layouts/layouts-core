<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\LayoutTypeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LayoutTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.layout.registry.layout_type';
    const TAG_NAME = 'netgen_block_manager.layout.layout_type';

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

        $layoutTypes = $container->getParameter('netgen_block_manager.layout_types');
        $blockDefinitions = $container->getParameter('netgen_block_manager.block_definitions');

        $this->validateLayoutTypes($layoutTypes, $blockDefinitions);
        $this->buildLayoutTypes($container, $layoutTypes);

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $layoutTypeServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($layoutTypeServices as $layoutTypeService => $tag) {
            $registry->addMethodCall(
                'addLayoutType',
                array(new Reference($layoutTypeService))
            );
        }
    }

    /**
     * Builds the layout type objects from provided configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $layoutTypes
     */
    protected function buildLayoutTypes(ContainerBuilder $container, array $layoutTypes)
    {
        foreach ($layoutTypes as $identifier => $layoutType) {
            $serviceIdentifier = sprintf('netgen_block_manager.layout.layout_type.%s', $identifier);

            $container->register($serviceIdentifier, LayoutType::class)
                ->setArguments(array($identifier, $layoutType))
                ->addTag('netgen_block_manager.layout.layout_type')
                ->setFactory(array(LayoutTypeFactory::class, 'buildLayoutType'));
        }
    }

    /**
     * Validates layout type config.
     *
     * @param array $layoutTypes
     * @param array $blockDefinitions
     *
     * @throws \RuntimeException If validation failed
     */
    protected function validateLayoutTypes(array $layoutTypes, array $blockDefinitions)
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
