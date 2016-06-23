<?php

namespace Netgen\BlockManager\Parameters;

interface ParameterInterface
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns the parameter constraints.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints();

    /**
     * Returns constraints that are common to all parameters.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getBaseConstraints();

    /**
     * Returns constraints that are specific to parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getParameterConstraints();

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
}
