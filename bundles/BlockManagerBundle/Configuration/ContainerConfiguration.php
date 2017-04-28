<?php

namespace Netgen\Bundle\BlockManagerBundle\Configuration;

use Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerConfiguration implements ConfigurationInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $parameters;

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

    /**
     * Returns if parameter exists in configuration.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        if (array_key_exists($parameterName, $this->parameters)) {
            return true;
        }

        return $this->container->hasParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName
        );
    }

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException If parameter is undefined
     *
     * @return mixed
     */
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
