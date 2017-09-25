<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class ParameterView extends View implements ParameterViewInterface
{
    public function getParameterValue()
    {
        return $this->parameters['parameter'];
    }

    public function getFallbackContext()
    {
        return self::CONTEXT_DEFAULT;
    }

    public function getIdentifier()
    {
        return 'parameter_view';
    }
}
