<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\View\View;

final class ParameterView extends View implements ParameterViewInterface
{
    public function getParameterValue(): Parameter
    {
        return $this->parameters['parameter'];
    }

    public function getIdentifier(): string
    {
        return 'parameter_view';
    }
}
