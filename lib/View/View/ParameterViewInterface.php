<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface ParameterViewInterface extends ViewInterface
{
    /**
     * Returns the parameter.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameterValue();
}
