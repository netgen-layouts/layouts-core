<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Stubs;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // This is a curated list of options that will be used
        // to test form matchers, add options as needed
        $resolver->setDefined('blockDefinition');
        $resolver->setDefined('queryType');
    }
}
