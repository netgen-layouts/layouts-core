<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;

class BlockDefinitionFactory
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    protected $parameterBuilderFactory;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface $parameterBuilderFactory
     */
    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the block definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function buildBlockDefinition(
        $identifier,
        BlockDefinitionHandlerInterface $handler,
        Configuration $config,
        array $configDefinitions
    ) {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            $configDefinitions
        );

        return new BlockDefinition($commonData);
    }

    /**
     * Builds the block definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @return \Netgen\BlockManager\Block\TwigBlockDefinitionInterface
     */
    public function buildTwigBlockDefinition(
        $identifier,
        TwigBlockDefinitionHandlerInterface $handler,
        Configuration $config,
        array $configDefinitions
    ) {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            $configDefinitions
        );

        return new TwigBlockDefinition($commonData);
    }

    /**
     * Builds the container definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @return \Netgen\BlockManager\Block\ContainerDefinitionInterface
     */
    public function buildContainerDefinition(
        $identifier,
        ContainerDefinitionHandlerInterface $handler,
        Configuration $config,
        array $configDefinitions
    ) {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            $configDefinitions
        );

        return new ContainerDefinition($commonData);
    }

    /**
     * Returns the data common to all block definition types.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @return array
     */
    protected function getCommonBlockDefinitionData(
        $identifier,
        BlockDefinitionHandlerInterface $handler,
        Configuration $config,
        array $configDefinitions
    ) {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return array(
            'identifier' => $identifier,
            'handler' => $handler,
            'config' => $config,
            'parameters' => $parameters,
            'configDefinitions' => $configDefinitions,
        );
    }
}
