<?php

namespace Netgen\BlockManager\Serializer\Values;

use Netgen\BlockManager\View\ViewInterface;

abstract class AbstractView extends AbstractVersionedValue
{
    /**
     * @var array
     */
    private $viewParameters = [];

    /**
     * Sets the view parameters.
     *
     * @param array $viewParameters
     */
    public function setViewParameters(array $viewParameters = [])
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

    /**
     * Returns the context that will be used to render this view.
     *
     * @return string
     */
    public function getContext()
    {
        return ViewInterface::CONTEXT_API;
    }
}
