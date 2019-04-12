<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Configuration;

/**
 * This is a lightweight wrapper around container parameters set by
 * Netgen Layouts (those starting with `netgen_layouts`).
 *
 * It allows accessing the parameter values from various places (mainly
 * Twig templates through a global variable), without directly accessing
 * the container.
 */
interface ConfigurationInterface
{
    public const PARAMETER_NAMESPACE = 'netgen_layouts';

    /**
     * Returns if parameter exists in configuration.
     */
    public function hasParameter(string $parameterName): bool;

    /**
     * Returns the parameter from configuration.
     *
     * @throws \Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter(string $parameterName);
}
