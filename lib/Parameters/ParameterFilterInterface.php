<?php

namespace Netgen\BlockManager\Parameters;

interface ParameterFilterInterface
{
    /**
     * Filters the parameter value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value);
}
