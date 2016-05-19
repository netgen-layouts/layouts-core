<?php

namespace Netgen\BlockManager\Serializer\Values;

interface ViewInterface extends VersionedValueInterface
{
    /**
     * Sets the view parameters.
     *
     * @param array $viewParameters
     */
    public function setViewParameters(array $viewParameters = array());

    /**
     * Returns the parameters transferred to the view.
     *
     * @return array
     */
    public function getViewParameters();

    /**
     * Returns the context that will be used to render this view.
     *
     * @return string
     */
    public function getContext();
}
