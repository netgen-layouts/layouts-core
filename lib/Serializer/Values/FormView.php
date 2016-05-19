<?php

namespace Netgen\BlockManager\Serializer\Values;

use Netgen\BlockManager\View\ViewInterface as BaseViewInterface;

class FormView extends AbstractFormView implements FormViewInterface
{
    /**
     * Returns the context that will be used to render this view.
     *
     * @return string
     */
    public function getContext()
    {
        return BaseViewInterface::CONTEXT_API_FORM;
    }
}
