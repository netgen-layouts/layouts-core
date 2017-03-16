<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class BlockDefinitionFactory
{
    /**
     * Builds the block definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public static function buildBlockDefinition(
        $identifier,
        BlockDefinitionHandlerInterface $handler,
        Configuration $config,
        ParameterBuilderInterface $parameterBuilder
    ) {
        $commonData = static::getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            clone $parameterBuilder
        );

        return new BlockDefinition($commonData);
    }

    /**
     * Builds the block definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return \Netgen\BlockManager\Block\TwigBlockDefinitionInterface
     */
    public static function buildTwigBlockDefinition(
        $identifier,
        TwigBlockDefinitionHandlerInterface $handler,
        Configuration $config,
        ParameterBuilderInterface $parameterBuilder
    ) {
        $commonData = static::getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            clone $parameterBuilder
        );

        return new TwigBlockDefinition($commonData);
    }

    /**
     * Builds the container definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return \Netgen\BlockManager\Block\ContainerDefinitionInterface
     */
    public static function buildContainerDefinition(
        $identifier,
        ContainerDefinitionHandlerInterface $handler,
        Configuration $config,
        ParameterBuilderInterface $parameterBuilder
    ) {
        $commonData = static::getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            clone $parameterBuilder
        );

        $data = static::getContainerDefinitionData($handler, clone $parameterBuilder);

        return new ContainerDefinition($data + $commonData);
    }

    /**
     * Returns the data common to all block definition types.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return array
     */
    protected static function getCommonBlockDefinitionData(
        $identifier,
        BlockDefinitionHandlerInterface $handler,
        Configuration $config,
        ParameterBuilderInterface $parameterBuilder
    ) {
        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return array(
            'identifier' => $identifier,
            'handler' => $handler,
            'config' => $config,
            'parameters' => $parameters,
        );
    }

    /**
     * Returns the data for building the container definition.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return array
     */
    protected static function getContainerDefinitionData(
        ContainerDefinitionHandlerInterface $handler,
        ParameterBuilderInterface $parameterBuilder
    ) {
        $placeholderIdentifiers = $handler->getPlaceholderIdentifiers();

        $placeholders = array();
        $placeholderBuilders = array();

        foreach ($placeholderIdentifiers as $placeholderIdentifier) {
            // Parameter builder is a one use object, hence the clone
            $placeholderBuilders[$placeholderIdentifier] = clone $parameterBuilder;
        }

        $handler->buildPlaceholderParameters($placeholderBuilders);

        foreach ($placeholderIdentifiers as $placeholderIdentifier) {
            $placeholders[$placeholderIdentifier] = new PlaceholderDefinition(
                array(
                    'identifier' => $placeholderIdentifier,
                    'parameters' => $placeholderBuilders[$placeholderIdentifier]->buildParameters(),
                )
            );
        }

        $handler->buildDynamicPlaceholderParameters($parameterBuilder);
        $dynamicPlaceholderParameters = $parameterBuilder->buildParameters();

        return array(
            'placeholders' => $placeholders,
            'dynamicPlaceholder' => new PlaceholderDefinition(
                array(
                    'identifier' => 'dynamic',
                    'parameters' => $dynamicPlaceholderParameters,
                )
            ),
        );
    }
}
