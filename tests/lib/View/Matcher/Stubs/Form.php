<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Stubs;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        // This is a curated list of options that will be used
        // to test form matchers, add options as needed
        $resolver->setDefined('block');
        $resolver->setDefined('query');
        $resolver->setDefined('configurable');
        $resolver->setDefined('config_key');
    }
}
