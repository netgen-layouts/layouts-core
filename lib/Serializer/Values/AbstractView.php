<?php

namespace Netgen\BlockManager\Serializer\Values;

abstract class AbstractView extends AbstractValue
{
    /**
     * @var array
     */
    protected $viewParameters = array();

    /**
     * Sets the view parameters.
     *
     * @param array $viewParameters
     */
    public function setViewParameters(array $viewParameters = array())
    {
        $this->viewParameters = $viewParameters;
    }

    /**
     * Returns the parameters transferred to the view.
     *
     * @return array
     */
    public function getViewParameters()
    {
        return $this->viewParameters;
    }
}
