<?php

namespace Netgen\BlockManager\Form;

use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\ParameterInterface;
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

            $parameter = $form->getOption('ngbm_parameter');
            if ($parameter instanceof ParameterInterface) {
                $disabled = !$parameter->getOption('translatable');
            }

            $form->setDisabled($disabled);

            if ($parameter instanceof ParameterInterface) {
                continue;
            }

            $this->disableFormsOnNonMainLocale($form);
        }
    }
}
