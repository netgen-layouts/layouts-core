<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
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
        // Parameter builder is a one use object, hence the clone
        $dynamicPlaceholderBuilder = clone $parameterBuilder;

        $handler->buildDynamicPlaceholderParameters($dynamicPlaceholderBuilder);
        $dynamicPlaceholderParameters = $dynamicPlaceholderBuilder->buildParameters();

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

        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return new BlockDefinition(
            array(
                'identifier' => $identifier,
                'handler' => $handler,
                'config' => $config,
                'placeholders' => $placeholders,
                'dynamicPlaceholder' => new PlaceholderDefinition(
                    array(
                        'identifier' => 'dynamic',
                        'parameters' => $dynamicPlaceholderParameters,
                    )
                ),
                'parameters' => $parameters,
            )
        );
    }
}
