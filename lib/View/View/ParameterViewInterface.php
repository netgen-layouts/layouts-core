<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\View\ViewInterface;

interface ParameterViewInterface extends ViewInterface
{
    /**
     * Returns the parameter.
     */
    public function getParameterValue(): Parameter;
}
