<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\ValueObject;

class Parameter extends ValueObject implements ParameterInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $isRequired;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var array
     */
    protected $groups;

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
     * Returns the parameter type.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
