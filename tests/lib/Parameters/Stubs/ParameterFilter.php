<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;

final class ParameterFilter implements ParameterFilterInterface
{
    /**
     * Filters the parameter value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value)
    {
        return strrev($value);
    }
}
