<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserDynamicType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('value_types'));
        $resolver->setAllowedTypes('value_types', 'array');
        $resolver->setDefault('value_types', array());
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
                    'forms.uri.link_type.url' => LinkValue::LINK_TYPE_URL,
                    'forms.uri.link_type.email' => LinkValue::LINK_TYPE_EMAIL,
                    'forms.uri.link_type.phone' => LinkValue::LINK_TYPE_PHONE,
                    'forms.uri.link_type.internal' => LinkValue::LINK_TYPE_INTERNAL,
                ),
                'choices_as_values' => true,
                'required' => true,
                'property_path' => 'linkType',
            )
        );

        $builder->add(
            LinkValue::LINK_TYPE_URL,
            UrlType::class,
            array(
                'label' => 'forms.uri.link_type.url',
            )
        );

        $builder->add(
            LinkValue::LINK_TYPE_EMAIL,
            EmailType::class,
            array(
                'label' => 'forms.uri.link_type.email',
            )
        );

        $builder->add(
            LinkValue::LINK_TYPE_PHONE,
            TextType::class,
            array(
                'label' => 'forms.uri.link_type.phone',
            )
        );

        $internalLinkForm = $builder->create(
            LinkValue::LINK_TYPE_INTERNAL,
            ContentBrowserDynamicType::class,
            array(
                'label' => 'forms.uri.link_type.internal',
                'item_types' => $options['value_types'],
                'error_bubbling' => false,
            )
        );

        $internalLinkForm->setDataMapper(new ItemLinkDataMapper());
        $builder->add($internalLinkForm);

        // We use the unmapped hidden field to collect the validation errors and
        // to redirect them to the right place when building the form view.
        // For "internal" form, this also needs "error_bubbling" set to false
        // since "internal" form is compound and errors are redirected to parent
        // by default.
        $builder->add(
            'link',
            HiddenType::class,
            array(
                'mapped' => false,
                'error_bubbling' => false,
                'property_path' => 'link',
            )
        );

        $builder->add(
            'link_suffix',
            TextType::class,
            array(
                'label' => 'forms.uri.link_suffix',
                'property_path' => 'linkSuffix',
            )
        );

        $builder->add(
            'new_window',
            CheckboxType::class,
            array(
                'label' => 'forms.uri.new_window',
                'required' => true,
                'property_path' => 'newWindow',
            )
        );
    }

    /**
     * Builds the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $linkType = $form->get('link_type')->getData();
        if (!$form->has($linkType)) {
            return;
        }

        $targetForm = $form->get($linkType);
        $linkErrors = $form->get('link')->getErrors();

        foreach ($linkErrors as $linkError) {
            $targetForm->addError($linkError);
        }
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
        return 'ngbm_link';
    }
}
