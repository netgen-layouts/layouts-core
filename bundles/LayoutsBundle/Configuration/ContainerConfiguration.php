<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Configuration;

use Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException;
use Symfony\Component\DependencyInjection\ContainerInterface;

use function array_key_exists;

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
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private ContainerInterface $container,
        private array $parameters = [],
    ) {}

    public function hasParameter(string $parameterName): bool
    {
        if (array_key_exists($parameterName, $this->parameters)) {
            return true;
        }

        return $this->container->hasParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName,
        );
    }

    public function getParameter(string $parameterName): mixed
    {
        if (!$this->hasParameter($parameterName)) {
            throw ConfigurationException::noParameter($parameterName);
        }

        if (array_key_exists($parameterName, $this->parameters)) {
            return $this->parameters[$parameterName];
        }

        return $this->container->getParameter(
            ConfigurationInterface::PARAMETER_NAMESPACE . '.' . $parameterName,
        );
    }
}
