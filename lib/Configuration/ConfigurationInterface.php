<?php

namespace Netgen\BlockManager\Configuration;

interface ConfigurationInterface
{
    const PARAMETER_NAMESPACE = 'netgen_block_manager';

    /**
     * Returns if parameter exists in configuration.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName);

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \InvalidArgumentException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName);

    /**
     * Returns the configuration for specified block
     *
     * @param string $definitionIdentifier
     *
     * @return array
     */
    public function getBlockConfig($definitionIdentifier);

    /**
     * Returns the configuration for specified layout
     *
     * @param string $layoutIdentifier
     *
     * @return array
     */
    public function getLayoutConfig($layoutIdentifier);
}
