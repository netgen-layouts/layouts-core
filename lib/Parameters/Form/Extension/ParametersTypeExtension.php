<?php

namespace Netgen\BlockManager\Parameters\Form\Extension;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form extension attaches the parameter being edited
 * to the form used to edit the parameter.
 */
class ParametersTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return FormType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('ngbm_parameter');
        $resolver->setAllowedTypes('ngbm_parameter', ParameterInterface::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['ngbm_parameter'])) {
            $view->vars['ngbm_parameter'] = $options['ngbm_parameter'];
        }
    }
}
