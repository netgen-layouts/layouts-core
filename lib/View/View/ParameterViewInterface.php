<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface ParameterViewInterface extends ViewInterface
{
    /**
     * Returns the parameter.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter
     */
    public function getParameterValue();
}
