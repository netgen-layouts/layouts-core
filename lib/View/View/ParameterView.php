<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\View\View;

final class ParameterView extends View implements ParameterViewInterface
{
    public function __construct(Parameter $parameter)
    {
        $this->parameters['parameter'] = $parameter;
    }

    public function getParameterValue(): Parameter
    {
        return $this->parameters['parameter'];
    }

    public static function getIdentifier(): string
    {
        return 'parameter';
    }
}
