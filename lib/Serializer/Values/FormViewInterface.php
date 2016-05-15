<?php

namespace Netgen\BlockManager\Serializer\Values;

interface FormViewInterface extends ViewInterface
{
    /**
     * Returns the form.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm();
}
