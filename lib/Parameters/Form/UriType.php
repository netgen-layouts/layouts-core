<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;
use Netgen\BlockManager\Parameters\Parameter\Uri;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class UriType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $linkTypes = array(
            'forms.uri.link_type.url' => Uri::LINK_TYPE_URL,
            'forms.uri.link_type.email' => Uri::LINK_TYPE_EMAIL,
            'forms.uri.link_type.internal' => Uri::LINK_TYPE_INTERNAL,
        );

        if (!$options['required']) {
            $linkTypes = array(
                'forms.uri.link_type.none' => 'none',
            ) + $linkTypes;
        }

        $builder->add(
            'link_type',
            ChoiceType::class,
            array(
                'label' => 'forms.uri.link_type',
                'choices' => $linkTypes,
                'choices_as_values' => true,
                'required' => true,
            )
        );

        $builder->add(
            Uri::LINK_TYPE_URL,
            UrlType::class,
            array(
                'label' => 'forms.uri.link_type.url',
            )
        );

        $builder->add(
            Uri::LINK_TYPE_EMAIL,
            EmailType::class,
            array(
                'label' => 'forms.uri.link_type.email',
            )
        );

        $builder->add(
            Uri::LINK_TYPE_INTERNAL,
            ContentBrowserType::class,
            array(
                'label' => 'forms.uri.link_type.internal',
                'item_type' => 'ezlocation',
            )
        );

        // We use the hidden field to collect the validation errors and
        // to show them in the right place using a template (in one of url,
        // email, internal forms) since we can't use error_mapping option
        // to do it automatically based on submitted data
        $builder->add(
            'link',
            HiddenType::class,
            array(
                'error_bubbling' => false,
            )
        );

        $builder->add(
            'link_suffix',
            TextType::class,
            array(
                'label' => 'forms.uri.link_suffix',
            )
        );

        $builder->add(
            'new_window',
            CheckboxType::class,
            array(
                'label' => 'forms.uri.new_window',
                'required' => true,
            )
        );
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'ngbm_uri';
    }
}
