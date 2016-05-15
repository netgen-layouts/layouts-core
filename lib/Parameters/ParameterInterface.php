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
     * @return array
     */
    public function getConstraints();

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired();
}
