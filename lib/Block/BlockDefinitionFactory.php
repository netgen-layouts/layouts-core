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
        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return new BlockDefinition(
            array(
                'identifier' => $identifier,
                'handler' => $handler,
                'config' => $config,
                'parameters' => $parameters,
            )
        );
    }
}
