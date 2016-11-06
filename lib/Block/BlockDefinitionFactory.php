<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
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
        $data = array(
            'identifier' => $identifier,
            'handler' => $handler,
            'config' => $config,
            'parameters' => function () use ($handler, $parameterBuilder) {
                $handler->buildParameters($parameterBuilder);
                $parameters = $parameterBuilder->buildParameters();

                return $parameters;
            },
        );

        if ($handler instanceof TwigBlockDefinitionHandlerInterface) {
            return new TwigBlockDefinition($data);
        }

        return new BlockDefinition($data);
    }
}
