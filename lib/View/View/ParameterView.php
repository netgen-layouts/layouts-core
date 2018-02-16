<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class ParameterView extends View implements ParameterViewInterface
{
    public function getParameterValue()
    {
        return $this->parameters['parameter'];
    }

    public function getIdentifier()
    {
        return 'parameter_view';
    }

    public function jsonSerialize()
    {
        return array(
            'name' => $this->getParameterValue()->getName(),
            'type' => $this->getParameterValue()->getParameterDefinition()->getType()->getIdentifier(),
        );
    }
}
