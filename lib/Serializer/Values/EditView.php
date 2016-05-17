<?php

namespace Netgen\BlockManager\Serializer\Values;

use Netgen\BlockManager\View\ViewInterface as BaseViewInterface;

class EditView extends View
{
    /**
     * Returns the context that will be used to render this view.
     *
     * @return array
     */
    public function getContext()
    {
        return BaseViewInterface::CONTEXT_API_EDIT;
    }
}
