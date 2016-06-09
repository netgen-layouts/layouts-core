<?php

namespace Netgen\BlockManager\View;

use Symfony\Component\Form\FormInterface;

class FormView extends View implements FormViewInterface
{
    /**
     * Constructor.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->valueObject = $form;
        $this->internalParameters['form'] = $form->createView();
    }

    /**
     * Returns the form.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->valueObject;
    }

    /**
     * Returns the form type.
     *
     * @return string
     */
    public function getFormType()
    {
        return get_class($this->valueObject->getConfig()->getType()->getInnerType());
    }

    /**
     * Returns the form view.
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function getFormView()
    {
        return $this->internalParameters['form'];
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
