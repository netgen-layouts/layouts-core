<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class FormView extends View implements FormViewInterface
{
    /**
     * Returns the form.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->parameters['formObject'];
    }

    /**
     * Returns the form type.
     *
     * @return string
     */
    public function getFormType()
    {
        return get_class($this->parameters['formObject']->getConfig()->getType()->getInnerType());
    }

    /**
     * Returns the form view.
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function getFormView()
    {
        return $this->parameters['form'];
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'form_view';
    }
}
