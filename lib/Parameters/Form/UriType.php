<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;
use Netgen\BlockManager\Parameters\Parameter\Uri;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class UriType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'link_type',
            ChoiceType::class,
            array(
                'label' => 'forms.uri.link_type',
                'choices' => array(
                    'forms.uri.link_type.url' => Uri::LINK_TYPE_URL,
                    'forms.uri.link_type.email' => Uri::LINK_TYPE_EMAIL,
                    'forms.uri.link_type.internal' => Uri::LINK_TYPE_INTERNAL,
                ),
                'choices_as_values' => true,
                'required' => true,
            )
        );

        $builder->add(
            'url',
            UrlType::class,
            array(
                'label' => 'forms.uri.link_type.url',
                'required' => true,
            )
        );

        $builder->add(
            'email',
            EmailType::class,
            array(
                'label' => 'forms.uri.link_type.email',
                'required' => true,
            )
        );

        $builder->add(
            'internal_link',
            ContentBrowserType::class,
            array(
                'label' => 'forms.uri.link_type.internal',
                'required' => true,
                'item_type' => 'ezlocation',
            )
        );

        $builder->add(
            'internal_link_suffix',
            TextType::class,
            array(
                'label' => 'forms.uri.internal_link_suffix',
            )
        );

        $builder->add(
            'open_in_new_window',
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
