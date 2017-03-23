<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class ConfigDefinition implements ConfigDefinitionInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $identifier
     * @param \Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler $handler
     */
    public function __construct($type, $identifier, ConfigDefinitionHandler $handler)
    {
        $this->type = $type;
        $this->identifier = $identifier;
        $this->handler = $handler;
    }

    /**
     * Returns the type of the config definition.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns config definition identifier.
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
}
