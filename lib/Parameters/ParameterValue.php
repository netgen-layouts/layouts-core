<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\ValueObject;

/**
 * Class that wraps the value of a parameter and enriches is with some additional properties like
 * the name of the parameter, parameter itself, flag indicating if the value is empty...
 */
final class ParameterValue extends ValueObject
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface
     */
    protected $parameter;

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
     * Returns the parameter.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter()
    {
        return $this->parameter;
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
