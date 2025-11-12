<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type;

use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\Layouts\Parameters\Value\LinkType as LinkTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver
            ->define('value_types')
            ->required()
            ->default([])
            ->allowedTypes('string[]');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'link_type',
            EnumType::class,
            [
                'class' => LinkTypeEnum::class,
                'label' => 'parameter.link.link_type',
                'choice_label' => static fn (LinkTypeEnum $linkType): string => match ($linkType) {
                    LinkTypeEnum::Url => 'parameter.link.link_type.url',
                    LinkTypeEnum::RelativeUrl => 'parameter.link.link_type.relative_url',
                    LinkTypeEnum::Email => 'parameter.link.link_type.email',
                    LinkTypeEnum::Phone => 'parameter.link.link_type.phone',
                    LinkTypeEnum::Internal => 'parameter.link.link_type.internal',
                },
                'required' => true,
                'property_path' => 'linkType',
            ],
        );

        $builder->add(
            LinkTypeEnum::Url->value,
            UrlType::class,
            [
                'label' => 'parameter.link.link_type.url',
                'required' => false,
                'default_protocol' => null,
            ],
        );

        $builder->add(
            LinkTypeEnum::RelativeUrl->value,
            TextType::class,
            [
                'label' => 'parameter.link.link_type.relative_url',
                'required' => false,
            ],
        );

        $builder->add(
            LinkTypeEnum::Email->value,
            EmailType::class,
            [
                'label' => 'parameter.link.link_type.email',
            ],
        );

        $builder->add(
            LinkTypeEnum::Phone->value,
            TextType::class,
            [
                'label' => 'parameter.link.link_type.phone',
            ],
        );

        $internalLinkForm = $builder->create(
            LinkTypeEnum::Internal->value,
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
        /** @var \Netgen\Layouts\Parameters\Value\LinkType $linkType */
        $linkType = $form->get('link_type')->getData();
        if ($linkType === null || !$form->has($linkType->value)) {
            return;
        }

        $targetForm = $form->get($linkType->value);

        foreach ($form->get('link')->getErrors() as $linkError) {
            $targetForm->addError($linkError);
        }
    }

    public function getBlockPrefix(): string
    {
        return 'nglayouts_link';
    }
}
