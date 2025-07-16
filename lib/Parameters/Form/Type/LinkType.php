<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type;

use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Symfony\Component\Form\AbstractType;
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

use function is_string;

final class LinkType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired(['value_types']);
        $resolver->setAllowedTypes('value_types', 'string[]');

        $resolver->setDefault('value_types', []);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'link_type',
            ChoiceType::class,
            [
                'label' => 'parameter.link.link_type',
                'choices' => [
                    'parameter.link.link_type.url' => LinkValue::LINK_TYPE_URL,
                    'parameter.link.link_type.relative_url' => LinkValue::LINK_TYPE_RELATIVE_URL,
                    'parameter.link.link_type.email' => LinkValue::LINK_TYPE_EMAIL,
                    'parameter.link.link_type.phone' => LinkValue::LINK_TYPE_PHONE,
                    'parameter.link.link_type.internal' => LinkValue::LINK_TYPE_INTERNAL,
                ],
                'required' => true,
                'property_path' => 'linkType',
            ],
        );

        $builder->add(
            LinkValue::LINK_TYPE_URL,
            UrlType::class,
            [
                'label' => 'parameter.link.link_type.url',
                'required' => false,
            ],
        );

        $builder->add(
            LinkValue::LINK_TYPE_RELATIVE_URL,
            TextType::class,
            [
                'label' => 'parameter.link.link_type.relative_url',
                'required' => false,
            ],
        );

        $builder->add(
            LinkValue::LINK_TYPE_EMAIL,
            EmailType::class,
            [
                'label' => 'parameter.link.link_type.email',
            ],
        );

        $builder->add(
            LinkValue::LINK_TYPE_PHONE,
            TextType::class,
            [
                'label' => 'parameter.link.link_type.phone',
            ],
        );

        $internalLinkForm = $builder->create(
            LinkValue::LINK_TYPE_INTERNAL,
            ContentBrowserDynamicType::class,
            [
                'label' => 'parameter.link.link_type.internal',
                'item_types' => $options['value_types'],
                'error_bubbling' => false,
            ],
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
            [
                'mapped' => false,
                'error_bubbling' => false,
                'property_path' => 'link',
            ],
        );

        $builder->add(
            'link_suffix',
            TextType::class,
            [
                'label' => 'parameter.link.link_suffix',
                'property_path' => 'linkSuffix',
            ],
        );

        $builder->add(
            'new_window',
            CheckboxType::class,
            [
                'label' => 'parameter.link.new_window',
                'required' => true,
                'property_path' => 'newWindow',
            ],
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $linkType = $form->get('link_type')->getData();
        if (!is_string($linkType) || !$form->has($linkType)) {
            return;
        }

        $targetForm = $form->get($linkType);

        foreach ($form->get('link')->getErrors() as $linkError) {
            $targetForm->addError($linkError);
        }
    }

    public function getBlockPrefix(): string
    {
        return 'nglayouts_link';
    }
}
