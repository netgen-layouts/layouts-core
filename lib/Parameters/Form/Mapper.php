<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    /**
     * Maps parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter)
    {
        return array();
    }

    /**
     * Allows the mapper to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterInterface $parameter, FormBuilderInterface $form)
    {
    }
}
