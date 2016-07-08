<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface FormViewInterface extends ViewInterface
{
    /**
     * Returns the form.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm();

    /**
     * Returns the form type.
     *
     * @return string
     */
    public function getFormType();

    /**
     * Returns the form view.
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function getFormView();
}
