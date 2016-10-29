<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\ValueObject;

class Parameter extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    protected $parameterDefinition;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $parameterType;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isEmpty;

    /**
     * Returns the parameter identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the parameter definition.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    public function getParameterDefinition()
    {
        return $this->parameterDefinition;
    }

    /**
     * Returns the parameter type.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterType()
    {
        return $this->parameterType;
    }

    /**
     * Returns the parameter value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns if the parameter is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->isEmpty;
    }

    /**
     * Returns the string representation of the parameter value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }
}
