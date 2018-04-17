<?php

namespace Netgen\BlockManager\Serializer\Values;

/**
 * Represents a serialized version of a view object.
 *
 * Allows rendering the view and injecting the rendered HTML in
 * the serialization output.
 */
interface ViewInterface extends VersionedValueInterface
{
    /**
     * Sets the view parameters.
     *
     * @param array $viewParameters
     */
    public function setViewParameters(array $viewParameters = []);

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
