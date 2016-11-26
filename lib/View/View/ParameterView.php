<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class ParameterView extends View implements ParameterViewInterface
{
    /**
     * Returns the parameter.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameterValue()
    {
        return $this->valueObject;
    }

    /**
     * Returns the view fallback context.
     *
     * @return string|null
     */
    public function getFallbackContext()
    {
        return self::CONTEXT_DEFAULT;
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'parameter_view';
    }
}
