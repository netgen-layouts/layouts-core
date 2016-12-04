<?php

namespace Netgen\BlockManager\Configuration;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ContainerConfiguration implements ConfigurationInterface
{
    use ContainerAwareTrait;

    /**
     * Returns if parameter exists in configuration.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return $this->container->hasParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName
        );
    }

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw new InvalidArgumentException(
                'parameterName',
                sprintf(
                    'Parameter "%s" does not exist in configuration.',
                    $parameterName
                )
            );
        }

        return $this->container->getParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName
        );
    }
}
