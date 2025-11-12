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
        $resolver->define('block');
        $resolver->define('query');
        $resolver->define('configurable');
        $resolver->define('config_key');
    }
}
