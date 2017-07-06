<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType;

use Symfony\Component\Form\FormBuilderInterface;

interface MapperInterface
{
    /**
     * Returns the form type that will be used to edit the value of this target type.
     *
     * @return string
     */
    public function getFormType();

    /**
     * Returns the form options.
     *
     * @return array
     */
    public function getFormOptions();

    /**
     * Handles the form for the target type.
     *
     * This is the place where you will usually add data mappers and transformers to the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     */
    public function handleForm(FormBuilderInterface $builder);
}
