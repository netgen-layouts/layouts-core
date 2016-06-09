<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Stubs;

use Netgen\BlockManager\View\FormViewInterface;
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
    }

    /**
     * Returns the form type.
     *
     * @return string
     */
    public function getFormType()
    {
        return 'form_type';
    }

    /**
     * Returns the form view.
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function getFormView()
    {
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'form_view';
    }
}
