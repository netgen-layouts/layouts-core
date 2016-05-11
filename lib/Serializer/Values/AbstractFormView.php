<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\Form\FormInterface;

abstract class AbstractFormView extends AbstractView
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * Sets the form.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * Returns the form.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
