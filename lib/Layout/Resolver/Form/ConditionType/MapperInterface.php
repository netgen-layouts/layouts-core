<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
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
     * Maps the form type options from provided condition type.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType
     *
     * @return array
     */
    public function mapOptions(ConditionTypeInterface $conditionType);

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
