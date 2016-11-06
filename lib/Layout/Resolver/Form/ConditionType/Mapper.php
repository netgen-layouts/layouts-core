<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    /**
     * Maps the form type options from provided target type.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType
     *
     * @return array
     */
    public function mapOptions(ConditionTypeInterface $conditionType)
    {
        return array(
            'required' => true,
            'constraints' => $conditionType->getConstraints(),
            'label' => sprintf('condition_type.%s.label', $conditionType->getType()),
            'error_bubbling' => false,
        );
    }

    /**
     * Handles the form for this condition type.
     *
     * This is the place where you will usually add data mappers and transformers to the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return array
     */
    public function handleForm(FormBuilderInterface $builder)
    {
    }
}
