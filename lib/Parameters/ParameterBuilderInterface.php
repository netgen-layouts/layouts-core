<?php

namespace Netgen\BlockManager\Parameters;

use Countable;

interface ParameterBuilderInterface extends Countable
{
    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the parameter type.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getType();

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns the parameter option with provided name.
     *
     * @param string $name
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the option does not exist
     *
     * @return mixed
     */
    public function getOption($name);

    /**
     * Returns if the parameter option with provided name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name);

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Sets if the parameter is required.
     *
     * @param bool $isRequired
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setRequired($isRequired);

    /**
     * Returns the default value of the parameter.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Sets the default value of the parameter.
     *
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setDefaultValue($defaultValue);

    /**
     * Returns the parameter label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Sets the parameter label.
     *
     * @param string $label
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setLabel($label);

    /**
     * Returns the parameter groups.
     *
     * @return array
     */
    public function getGroups();

    /**
     * Sets the parameter groups.
     *
     * @param array $groups
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setGroups(array $groups);

    /**
     * Adds the parameter to the builder.
     *
     * @param string $name
     * @param string $type
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function add($name, $type, array $options = array());

    /**
     * Returns the builder for parameter with provided name.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function get($name);

    /**
     * Returns the builders for all parameters, optionally filtered by the group.
     *
     * @param string $group
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface[]
     */
    public function all($group = null);

    /**
     * Returns if the builder has the parameter with provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Removes the parameter from the builder.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function remove($name);

    /**
     * Returns the count of the parameters.
     *
     * @return int
     */
    public function count();

    /**
     * Builds the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function buildParameters();
}
