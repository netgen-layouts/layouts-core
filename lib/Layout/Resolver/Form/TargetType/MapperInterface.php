<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
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
     * Maps the form type options from provided target type.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType
     *
     * @return array
     */
    public function mapOptions(TargetTypeInterface $targetType);

    /**
     * Handles the form for this target type.
     *
     * This is the place where you will usually add data mappers and transformers to the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType
     *
     * @return array
     */
    public function handleForm(FormBuilderInterface $builder, TargetTypeInterface $targetType);
}
