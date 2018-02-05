<?php

namespace Netgen\BlockManager\Parameters;

interface ParameterDefinitionInterface
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
     * Returns if the provided parameter option exists.
     *
     * @param string $option
     *
     * @return bool
     */
    public function hasOption($option);

    /**
     * Returns the provided parameter option.
     *
     * @param string $option
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If option does not exist
     *
     * @return mixed
     */
    public function getOption($option);

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Returns the parameter label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns the list of all parameter groups.
     *
     * @return array
     */
    public function getGroups();
}
