<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Stubs;

use Netgen\BlockManager\View\View;
use Netgen\BlockManager\View\View\FormViewInterface;

final class FormView extends View implements FormViewInterface
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
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'form_view';
    }
}
