<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\View\View;

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
