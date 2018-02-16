<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class FormView extends View implements FormViewInterface
{
    public function getForm()
    {
        return $this->parameters['form_object'];
    }

    public function getFormType()
    {
        return get_class($this->parameters['form_object']->getConfig()->getType()->getInnerType());
    }

    public function getFormView()
    {
        return $this->parameters['form'];
    }

    public function getIdentifier()
    {
        return 'form_view';
    }

    public function jsonSerialize()
    {
        return array(
            'formName' => $this->getForm()->getName(),
            'formType' => $this->getFormType(),
        );
    }
}
