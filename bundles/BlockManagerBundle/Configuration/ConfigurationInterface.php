<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Configuration;

/**
 * This is a lightweight wrapper around container parameters set by
 * Netgen Layouts (those starting with `netgen_block_manager`).
 *
 * It allows accessing the parameter values from various places (mainly
 * Twig templates through a global variable), without directly accessing
 * the container.
 */
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
     * @throws \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName);
}
