<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    /**
     * Maps the form type options from provided target type.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType
     *
     * @return array
     */
    public function mapOptions(TargetTypeInterface $targetType)
    {
        return array(
            'required' => true,
            'constraints' => $targetType->getConstraints(),
            'label' => sprintf('target_type.%s.label', $targetType->getType()),
            'error_bubbling' => false,
        );
    }

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
    public function handleForm(FormBuilderInterface $builder, TargetTypeInterface $targetType)
    {
    }
}
