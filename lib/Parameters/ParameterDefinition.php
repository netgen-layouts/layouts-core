<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Value;

/**
 * The definition of a parameter, specifying its name, type and various options.
 *
 * This class is considered final and should not be extended.
 */
class ParameterDefinition extends Value
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
    protected $options = [];

    /**
     * @var bool
     */
    protected $isRequired = false;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var \Symfony\Component\Validator\Constraint[]
     */
    protected $constraints = [];

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
     * Returns if the provided parameter option exists.
     *
     * @param string $option
     *
     * @return bool
     */
    public function hasOption($option)
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * Returns the provided parameter option.
     *
     * @param string $option
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If option does not exist
     *
     * @return mixed
     */
    public function getOption($option)
    {
        if (!$this->hasOption($option)) {
            throw ParameterException::noOption($option);
        }

        return $this->options[$option];
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
     * Returns the parameter label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the list of all parameter groups.
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Returns the list of constraints.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return $this->constraints;
    }
}
