<?php

namespace Netgen\BlockManager\Tests\Layout\Container\Stubs;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface;
use Netgen\BlockManager\Layout\Container\PlaceholderDefinition;

class ContainerDefinition implements ContainerDefinitionInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Tests\Layout\Container\Stubs\ContainerDefinitionHandler
     */
    protected $handler;

    /**
     * @var array
     */
    protected $viewTypes;

    /**
     * @var \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface[]
     */
    protected $placeholders;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $viewTypes
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandlerInterface $handler
     */
    public function __construct($identifier, array $viewTypes = array(), ContainerDefinitionHandlerInterface $handler = null)
    {
        $this->identifier = $identifier;
        $this->viewTypes = $viewTypes;

        $this->handler = $handler ?: new ContainerDefinitionHandler();

        $this->placeholders = array();

        foreach ($this->handler->getPlaceholderIdentifiers() as $placeholderIdentifier) {
            $this->placeholders[$placeholderIdentifier] = new PlaceholderDefinition(
                array(
                    'identifier' => $placeholderIdentifier,
                    'parameters' => $this->handler->getParameters(),
                )
            );
        }
    }

    /**
     * Returns container definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->handler->getParameters();
    }

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($parameterName)
    {
        if ($this->hasParameter($parameterName)) {
            return $this->handler->getParameters()[$parameterName];
        }

        throw new InvalidArgumentException('parameterName', 'Parameter is missing.');
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return isset($this->handler->getParameters()[$parameterName]);
    }

    /**
     * Returns the container definition configuration.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration
     */
    public function getConfig()
    {
        $viewTypes = array();
        foreach ($this->viewTypes as $viewType => $itemTypes) {
            $viewTypes[$viewType] = new ViewType(
                array(
                    'identifier' => $viewType,
                    'name' => $viewType,
                )
            );
        }

        return new Configuration(
            array(
                'identifier' => $this->identifier,
                'viewTypes' => $viewTypes,
            )
        );
    }

    /**
     * Returns container placeholder definitions.
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface[]
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Returns a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException if the placeholder does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface
     */
    public function getPlaceholder($placeholderIdentifier)
    {
        return $this->placeholders[$placeholderIdentifier];
    }

    /**
     * Returns if container definition has a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @return bool
     */
    public function hasPlaceholder($placeholderIdentifier)
    {
        return isset($this->placeholders[$placeholderIdentifier]);
    }
}
