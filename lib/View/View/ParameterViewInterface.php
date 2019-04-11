<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\View\ViewInterface;

interface ParameterViewInterface extends ViewInterface
{
    /**
     * Returns the parameter.
     */
    public function getParameterValue(): Parameter;
}
