<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;

final class ParameterFilter implements ParameterFilterInterface
{
    public function filter($value)
    {
        return strrev($value);
    }
}
