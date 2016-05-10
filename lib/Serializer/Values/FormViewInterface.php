<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\Form\FormInterface;

interface FormViewInterface extends ViewInterface
{
    /**
     * Sets the form.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     */
    public function setForm(FormInterface $form);

    /**
     * Returns the form.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm();
}
