<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Value;

final class Parameter extends Value
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinition
     */
    protected $parameterDefinition;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isEmpty;

    /**
     * Returns the string representation of the parameter value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the parameter definition.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition
     */
    public function getParameterDefinition()
    {
        return $this->parameterDefinition;
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
     * Returns if the parameter value is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->isEmpty;
    }
}
