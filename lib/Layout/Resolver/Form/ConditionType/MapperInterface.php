<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType;

use Symfony\Component\Form\FormBuilderInterface;

interface MapperInterface
{
    /**
     * Returns the form type that will be used to edit the value of this condition type.
     *
     * @return string
     */
    public function getFormType();

    /**
     * Returns the form type options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Handles the form for this condition type.
     *
     * This is the place where you will usually add data mappers and transformers to the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return array
     */
    public function handleForm(FormBuilderInterface $builder);
}
