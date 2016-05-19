<?php

namespace Netgen\BlockManager\Serializer\Values;

use Netgen\BlockManager\View\ViewInterface as BaseViewInterface;

class View extends AbstractView implements ViewInterface
{
    /**
     * Returns the context that will be used to render this view.
     *
     * @return string
     */
    public function getContext()
    {
        return BaseViewInterface::CONTEXT_API_VIEW;
    }
}
