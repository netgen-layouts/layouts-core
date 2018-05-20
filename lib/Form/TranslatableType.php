<?php

namespace Netgen\BlockManager\Form;

use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Form\FormBuilderInterface;

abstract class TranslatableType extends AbstractType
{
    /**
     * Disables all inputs which are not translatable in the form.
     *
     * Basically, only translatable parameters are left enabled.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     */
    protected function disableFormsOnNonMainLocale(FormBuilderInterface $builder)
    {
        foreach ($builder as $form) {
            /** @var \Symfony\Component\Form\FormBuilderInterface $form */
            $innerType = $form->getType()->getInnerType();
            $disabled = !$innerType instanceof ParametersType;

            $parameterDefinition = $form->getOption('ngbm_parameter_definition');
            if ($parameterDefinition instanceof ParameterDefinition) {
                $disabled = $parameterDefinition->getOption('translatable') !== true;
            }

            $form->setDisabled($disabled);

            if ($parameterDefinition instanceof ParameterDefinition) {
                continue;
            }

            $this->disableFormsOnNonMainLocale($form);
        }
    }
}
