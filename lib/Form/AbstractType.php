<?php

namespace Netgen\BlockManager\Form;

use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract form used by all other forms in Netgen Layouts,
 * sets the options common to all forms, like the translation domain.
 */
abstract class AbstractType extends BaseAbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation_domain', 'ngbm');
    }
}
