<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;
use Netgen\BlockManager\Parameters\Parameter;

class ParameterView extends View implements ParameterViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Parameter $parameter
     */
    public function __construct(Parameter $parameter)
    {
        $this->valueObject = $parameter;

        $this->internalParameters['parameter'] = $parameter;
    }

    /**
     * Returns the parameter.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter
     */
    public function getParameterValueObject()
    {
        return $this->valueObject;
    }

    /**
     * Returns the view fallback context.
     *
     * @return string
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
