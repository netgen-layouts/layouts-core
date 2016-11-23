<?php

namespace Netgen\BlockManager\Form;

use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType extends BaseAbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_forms';

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation_domain', static::TRANSLATION_DOMAIN);
    }
}
