<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class ContainerTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.configuration.registry.container_type';
    const TAG_NAME = 'netgen_block_manager.configuration.container_type';

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

        $containerTypes = $container->getParameter('netgen_block_manager.container_types');
        $containerDefinitions = $container->getParameter('netgen_block_manager.container_definitions');

        $containerTypes = $this->generateContainerTypeConfig($containerTypes, $containerDefinitions);
        $container->setParameter('netgen_block_manager.container_types', $containerTypes);

        $this->validateContainerTypes($containerTypes, $containerDefinitions);
        $this->buildContainerTypes($container, $containerTypes);

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $containerTypeServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($containerTypeServices as $containerTypeService => $tag) {
            $registry->addMethodCall(
                'addContainerType',
                array(new Reference($containerTypeService))
            );
        }
    }

    /**
     * Generates the container type configuration from provided container definitions.
     *
     * @param array $containerTypes
     * @param array $containerDefinitions
     *
     * @return array
     */
    protected function generateContainerTypeConfig(array $containerTypes, array $containerDefinitions)
    {
        foreach ($containerDefinitions as $identifier => $containerDefinition) {
            if (
                !empty($containerTypes[$identifier]['definition_identifier']) &&
                $containerTypes[$identifier]['definition_identifier'] !== $identifier
            ) {
                // We skip the container types which have been completely redefined
                // i.e. had the container definition identifier changed
                continue;
            }

            if (!isset($containerTypes[$identifier])) {
                $containerTypes[$identifier] = array(
                    'name' => $containerDefinition['name'],
                    'enabled' => $containerDefinition['enabled'],
                    'definition_identifier' => $identifier,
                    'defaults' => array(),
                );

                continue;
            }

            $containerTypes[$identifier] = $containerTypes[$identifier] + array(
                'name' => $containerDefinition['name'],
                'enabled' => $containerDefinition['enabled'],
                'definition_identifier' => $identifier,
            );
        }

        return $containerTypes;
    }

    /**
     * Builds the container type objects from provided configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $containerTypes
     */
    protected function buildContainerTypes(ContainerBuilder $container, array $containerTypes)
    {
        foreach ($containerTypes as $identifier => $containerType) {
            if (!$containerType['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.configuration.container_type.%s', $identifier);

            $container
                ->setDefinition(
                    $serviceIdentifier,
                    new DefinitionDecorator('netgen_block_manager.configuration.container_type')
                )
                ->setArguments(
                    array(
                        $identifier,
                        $containerType,
                        new Reference(
                            sprintf(
                                'netgen_block_manager.container.container_definition.%s',
                                $containerType['definition_identifier']
                            )
                        ),
                    )
                )
                ->addTag('netgen_block_manager.configuration.container_type')
                ->setAbstract(false);
        }
    }

    /**
     * Validates container type config.
     *
     * @param array $containerTypes
     * @param array $containerDefinitions
     *
     * @throws \RuntimeException If validation failed
     */
    protected function validateContainerTypes(array $containerTypes, array $containerDefinitions)
    {
        foreach ($containerTypes as $identifier => $containerType) {
            if (!isset($containerDefinitions[$containerType['definition_identifier']])) {
                throw new RuntimeException(
                    sprintf(
                        'Container definition "%s" used in "%s" container type does not exist.',
                        $containerType['definition_identifier'],
                        $identifier
                    )
                );
            }
        }
    }
}
