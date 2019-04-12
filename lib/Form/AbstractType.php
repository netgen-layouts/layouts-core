<?php

declare(strict_types=1);

namespace Netgen\Layouts\Form;

use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract form used by all other forms in Netgen Layouts,
 * sets the options common to all forms, like the translation domain.
 */
abstract class AbstractType extends BaseAbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');
    }
}
