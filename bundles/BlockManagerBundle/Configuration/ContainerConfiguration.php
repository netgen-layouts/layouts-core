<?php

namespace Netgen\Bundle\BlockManagerBundle\Configuration;

use Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This is a default implementation of ConfigurationInterface,
 * allowing some parameters to be injected into constructor and
 * returned first if they exist, before checking the container.
 *
 * @final
 */
class ContainerConfiguration implements ConfigurationInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $parameters
     */
    public function __construct(ContainerInterface $container, array $parameters = array())
    {
        $this->container = $container;
        $this->parameters = $parameters;
    }

    public function hasParameter($parameterName)
    {
        if (array_key_exists($parameterName, $this->parameters)) {
            return true;
        }

        return $this->container->hasParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName
        );
    }

    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw ConfigurationException::noParameter($parameterName);
        }

        if (array_key_exists($parameterName, $this->parameters)) {
            return $this->parameters[$parameterName];
        }

        return $this->container->getParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName
        );
    }
}
