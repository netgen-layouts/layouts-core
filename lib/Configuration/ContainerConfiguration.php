<?php

namespace Netgen\BlockManager\Configuration;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Netgen\BlockManager\Exceptions\InvalidArgumentException;

class ContainerConfiguration implements ConfigurationInterface
{
    use ContainerAwareTrait;

    /**
     * Returns if parameter exists in configuration
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
     * Returns the parameter from configuration
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw new InvalidArgumentException(
                'parameterName',
                $parameterName,
                'Parameter does not exist in configuration.'
            );
        }

        return $this->container->getParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName
        );
    }
}
