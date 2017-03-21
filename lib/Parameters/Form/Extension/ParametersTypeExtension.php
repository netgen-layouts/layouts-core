<?php

namespace Netgen\BlockManager\Parameters\Form\Extension;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametersTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return FormType::class;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('ngbm_parameter');
        $resolver->setAllowedTypes('ngbm_parameter', ParameterInterface::class);
    }

    /**
     * Builds the view.
     *
     * This method is called after the extended type has built the view to
     * further modify it.
     *
     * @param \Symfony\Component\Form\FormView $view The view
     * @param \Symfony\Component\Form\FormInterface $form The form
     * @param array $options The options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['ngbm_parameter'])) {
            $view->vars['ngbm_parameter'] = $options['ngbm_parameter'];
        }
    }
}
