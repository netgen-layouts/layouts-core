<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType;

use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    /**
     * Returns the form options.
     *
     * @return array
     */
    public function getFormOptions()
    {
        return array();
    }

    /**
     * Handles the form for the target type.
     *
     * This is the place where you will usually add data mappers and transformers to the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     */
    public function handleForm(FormBuilderInterface $builder)
    {
    }
}
